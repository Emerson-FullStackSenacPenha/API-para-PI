<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "DataBaseConecta.php";

class Usuario {
    private $conn;
    private $tabela = "usuario";

    public $id;
    public $nome;
    public $email;
    public $senha;
    public $telefone;
    public $data_cadastro;
    public $tipo_usuario;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function sanitize($value, $max = 255) {
        $value = trim($value ?? '');
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        return mb_substr($value, 0, $max);
    }

    public function lerTodos() {
        try {
            $query = "SELECT id, nome, email, telefone, data_cadastro, tipo_usuario FROM {$this->tabela} ORDER BY id ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erro lerTodos: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["message" => "Erro ao buscar usuários."]);
            exit;
        }
    }

    public function lerUm() {
        try {
            $this->id = filter_var($this->id, FILTER_VALIDATE_INT);
            if (!$this->id) {
                http_response_code(400);
                echo json_encode(["message" => "ID inválido."]);
                exit;
            }

            $query = "SELECT id, nome, email, telefone, data_cadastro, tipo_usuario FROM {$this->tabela} WHERE id = ? LIMIT 0,1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro lerUm: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["message" => "Erro ao buscar usuário."]);
            exit;
        }
    }

    public function criar() {
        try {
            $query = "INSERT INTO {$this->tabela} 
                      SET nome=:nome, email=:email, senha=:senha, telefone=:telefone, data_cadastro=:data_cadastro, tipo_usuario=:tipo_usuario";
            $stmt = $this->conn->prepare($query);

            // 🧤 Sanitização dos dados
            $this->nome = $this->sanitize($this->nome, 150);
            $this->email = filter_var($this->email, FILTER_VALIDATE_EMAIL);
            $this->telefone = $this->sanitize($this->telefone, 20);
            $this->tipo_usuario = $this->sanitize($this->tipo_usuario, 30);

            if (!$this->email) {
                http_response_code(400);
                echo json_encode(["message" => "E-mail inválido."]);
                exit;
            }

            $hashed_password = password_hash($this->senha, PASSWORD_DEFAULT);

            $stmt->bindParam(":nome", $this->nome);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":senha", $hashed_password);
            $stmt->bindParam(":telefone", $this->telefone);
            $stmt->bindParam(":data_cadastro", $this->data_cadastro);
            $stmt->bindParam(":tipo_usuario", $this->tipo_usuario);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro criar: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["message" => "Erro ao criar usuário."]);
            exit;
        }
    }

    public function atualizar() {
        try {
            $this->id = filter_var($this->id, FILTER_VALIDATE_INT);
            if (!$this->id) {
                http_response_code(400);
                echo json_encode(["message" => "ID inválido."]);
                exit;
            }

            $this->nome = $this->sanitize($this->nome, 150);
            $this->email = filter_var($this->email, FILTER_VALIDATE_EMAIL);
            $this->telefone = $this->sanitize($this->telefone, 20);
            $this->tipo_usuario = $this->sanitize($this->tipo_usuario, 30);

            if (!$this->email) {
                http_response_code(400);
                echo json_encode(["message" => "E-mail inválido."]);
                exit;
            }

            $query = "UPDATE {$this->tabela}
                      SET nome=:nome, email=:email, telefone=:telefone, tipo_usuario=:tipo_usuario
                      WHERE id=:id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            $stmt->bindParam(":nome", $this->nome);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":telefone", $this->telefone);
            $stmt->bindParam(":tipo_usuario", $this->tipo_usuario);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro atualizar: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar usuário."]);
            exit;
        }
    }

    public function deletar() {
        try {
            $this->id = filter_var($this->id, FILTER_VALIDATE_INT);
            if (!$this->id) {
                http_response_code(400);
                echo json_encode(["message" => "ID inválido."]);
                exit;
            }

            $query = "DELETE FROM {$this->tabela} WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro deletar: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["message" => "Erro ao deletar usuário."]);
            exit;
        }
    }
}

// ---------------- CONTROLADOR ---------------- //
$db = (new Database())->conectar();
$usuario = new Usuario($db);
$metodo = $_SERVER["REQUEST_METHOD"];

switch ($metodo) {
    case "GET":
        if (!empty($_GET["id"])) {
            $usuario->id = $_GET["id"];
            $dados = $usuario->lerUm();
            echo json_encode($dados ?: ["message" => "Usuário não encontrado."]);
        } else {
            $stmt = $usuario->lerTodos();
            echo json_encode($stmt->fetchAll());
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(["message" => "Dados inválidos."]);
            exit;
        }
        $usuario->nome = $data["nome"] ?? '';
        $usuario->email = $data["email"] ?? '';
        $usuario->senha = $data["senha"] ?? '';
        $usuario->telefone = $data["telefone"] ?? '';
        $usuario->data_cadastro = date("Y-m-d H:i:s");
        $usuario->tipo_usuario = "cliente";

        if ($usuario->criar()) {
            http_response_code(201);
            echo json_encode(["message" => "Usuário criado com sucesso!", "id" => $usuario->id]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Falha ao criar usuário."]);
        }
        break;

    case "PUT":
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || empty($data["id"])) {
            http_response_code(400);
            echo json_encode(["message" => "ID não fornecido."]);
            exit;
        }
        $usuario->id = $data["id"];
        $usuario->nome = $data["nome"] ?? '';
        $usuario->email = $data["email"] ?? '';
        $usuario->telefone = $data["telefone"] ?? '';
        $usuario->tipo_usuario = $data["tipo_usuario"] ?? '';

        if ($usuario->atualizar()) {
            echo json_encode(["message" => "Usuário atualizado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Falha ao atualizar."]);
        }
        break;

    case "DELETE":
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data["id"])) {
            http_response_code(400);
            echo json_encode(["message" => "ID não fornecido."]);
            exit;
        }
        $usuario->id = $data["id"];
        if ($usuario->deletar()) {
            echo json_encode(["message" => "Usuário deletado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Falha ao deletar usuário."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Método não permitido."]);
}
?>
