<?php
class Database {
    private $servidor = "localhost";
    private $banco    = "api_usuario_pronto_saudavel";
    private $usuario  = "root";
    private $senha    = "";
    
    public $conexao;

    public function conectar() {
        $this->conexao = null;

        try {
            $dsn = "mysql:host={$this->servidor};dbname={$this->banco};charset=utf8mb4";
            $this->conexao = new PDO($dsn, $this->usuario, $this->senha);
            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conexao->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // ⚙️ segurança extra
        } catch (PDOException $erro) {
            // Não mostrar mensagem sensível ao usuário
            error_log("Erro DB: " . $erro->getMessage());
            http_response_code(500);
            die(json_encode(["message" => "Erro interno ao conectar ao banco."]));
        }

        return $this->conexao;
    }
}
?>
