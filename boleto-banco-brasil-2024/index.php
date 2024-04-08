<?php
// Função para validar o token de autenticação
function validar_token()
{
    // Obtém o token de validação da requisição POST
    $token = "";

    // Configura a requisição para a API do Banco do Brasil
    $url = 'https://oauth.bb.com.br/oauth/token?grant_type=client_credentials&scope=payments.direct-debit-authorization-request%20cobrancas.boletos-requisicao%20cobrancas.boletos-info%20cob.rite%20cob.read%20pix.write%20pix.read%20webhook.read%20webhook.write%20pix.arrecadacao-requisicao%20pix.arrecadacao-info&gw-dev-app-key=sua-chave-key';
    $data = array('token' => $token);
    $options = array(
        'http' => array(
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\nAuthorization: autorizacao-Basic",
            'method'  => 'POST',
            'content' => http_build_query($data),

        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === false) {
        // Falha na requisição
        return json_encode(array('success' => false, 'message' => 'Erro ao validar token.'));
    } else {
        // Sucesso na requisição
        return $result;
    }
}

// Função para gerar boleto
function gerarBoleto($token, $id,  $email_cliente, $cpf_cliente, $telefone_cliente, $nome_cliente,  $uf_cliente)
{
    // Remova acentos do CPF
    $cpf = str_replace(['.', '-'], '', $cpf_cliente);

    // Remova acentos e espaços do telefone
    $telefone = str_replace(' ', '', $telefone_cliente); // Remove espaços em branco
    $telefone = preg_replace('/[\(\)\-\s]/', '', $telefone); // Remove parênteses, hífen e traço

    // Obtém a data de vencimento do boleto
    $datavencimento = date('Y-m-d', strtotime('+1 days'));
    $dia = date('d');
    $mes = date('m');
    $ano = date('Y');

    // Cria o código do boleto
    $codigo_boleto = str_pad('00000000000' . date('H') . $dia . $mes, 20, '0', STR_PAD_RIGHT);

    // Dados do boleto em formato JSON
    $data = '{
    "numeroConvenio": 123456,
    "numeroCarteira": 00,
    "numeroVariacaoCarteira": 00,
    "codigoModalidade": 0,
    "dataEmissao": "' . $dia . '.' . $mes . '.' . $ano . '",
    "dataVencimento": "' . $datavencimento . '",
    "valorOriginal": 100,
    "valorAbatimento": 0,
    "numeroTituloCliente": "' . $codigo_boleto . '",
    "indicadorPix": "S",
    "mensagemBloquetoOcorrencia": "Mensagem Apresentada no Boleto",
    "descricaoTipoTitulo": "Descrição do Boleto",    
    "pagador": {
      "tipoInscricao": 1, 
      "numeroInscricao": "' . $cpf . '",
      "nome": "' . $nome_cliente . '",
      
      "uf": "' . $uf_cliente . '",
      "telefone": "' . $telefone . '",
      "textoEnderecoEmail": "' . $email_cliente . '"
    },
    "beneficiarioFinal": {
      "tipoInscricao": 2,
      "numeroInscricao": "00000000000000",
      "nome": "Nome do recebedor do Boleto"
    }
  }';

    // URL da API do Banco do Brasil para cadastrar boletos
    $url = 'https://api.bb.com.br/cobrancas/v2/boletos?gw-dev-app-key=sua-chave-key';

    // Inicializa a requisição CURL
    $ch = curl_init($url);

    // Define as opções da requisição
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token,
        'Content-Length: ' . strlen($data)
    ));

    // Executa a requisição e obtém a resposta
    $response = curl_exec($ch);

    // Verifica se houve algum erro na requisição
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        // Lida com o erro de alguma forma
    } else {
        // Processa a resposta
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Lida com o código de resposta HTTP e a resposta do Banco do Brasil
    }

    // Fecha a conexão CURL
    curl_close($ch);

    return $response;
}

// Valida o token de autenticação
$token = validar_token();

// Extrai o token da resposta
$inicio = strpos($token, '"access_token":"') + strlen('"access_token":"');
$fim = strpos($token, '","token_type"');
$token = substr($token, $inicio, $fim - $inicio);

// Gera o boleto com o token válido
$string = gerarBoleto($token, 1, "email-cliente", "cpf-com-ou-sem-acento", "telefone", "nome-cliente",  "UF");

// Decodifica a resposta JSON
$objeto = json_decode(strval($string), true);

// Obtém os dados do boleto
$qrCode = $objeto['qrCode'];
$txId = $qrCode['txId'];
$emv = $qrCode['emv'];
$numero = $objeto['numero'];
$linhaDigitavel = $objeto['linhaDigitavel'];
$codigoBarraNumerico = $objeto['codigoBarraNumerico'];
$numeroContratoCobranca = $objeto['numeroContratoCobranca'];
?>
