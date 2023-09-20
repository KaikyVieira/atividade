<?php
// Criar uma variável para armazenar os usuários cadastrados
$usuarios = array();

// Criar uma função para verificar se um endereço de e-mail é válido
function email_valido($email) {
  // Usar a função filter_var do php para validar o e-mail
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Criar uma função para verificar se uma senha tem pelo menos 8 caracteres
function senha_valida($senha) {
  // Usar a função strlen do php para obter o tamanho da senha
  return strlen($senha) >= 8;
}

// Criar uma função para verificar se um usuário existe na lista de usuários
function usuario_existe($nome) {
  // Usar a variável global $usuarios para acessar a lista de usuários
  global $usuarios;
  // Usar um laço for para percorrer a lista de usuários
  for ($i = 0; $i < count($usuarios); $i++) {
    // Se o nome do usuário for igual ao nome informado, retornar verdadeiro
    if ($usuarios[$i]['nome'] == $nome) {
      return true;
    }
  }
  // Se não encontrar o usuário, retornar falso
  return false;
}

// Criar uma função para verificar se as credenciais de login estão corretas
function login_correto($nome, $senha) {
  // Usar a variável global $usuarios para acessar a lista de usuários
  global $usuarios;
  // Usar um laço for para percorrer a lista de usuários
  for ($i = 0; $i < count($usuarios); $i++) {
    // Se o nome e a senha do usuário forem iguais aos informados, retornar verdadeiro
    if ($usuarios[$i]['nome'] == $nome && $usuarios[$i]['senha'] == $senha) {
      return true;
    }
  }
  // Se não encontrar o usuário ou a senha estiver incorreta, retornar falso
  return false;
}

// Criar uma função para cadastrar um novo usuário na lista de usuários
function cadastrar_usuario($nome, $email, $senha) {
  // Usar a variável global $usuarios para acessar a lista de usuários
  global $usuarios;
  // Criar um array associativo com os dados do usuário
  $usuario = array(
    'nome' => $nome,
    'email' => $email,
    'senha' => $senha
  );
  // Adicionar o usuário na lista de usuários
  array_push($usuarios, $usuario);
}

// Verificar se o método da solicitação é POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Verificar se a página é de cadastro ou de login
  if (isset($_POST['cadastro'])) {
    // Obter os dados do formulário de cadastro
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    // Verificar se os dados são válidos 
    if (email_valido($email) && senha_valida($senha) && !usuario_existe($nome)) {
      // Cadastrar o usuário na lista de usuários
      cadastrar_usuario($nome, $email, $senha);
      // Redirecionar para a página de login com uma mensagem de sucesso
      header('Location: login.phtml?sucesso=Cadastro realizado com sucesso!');
    } else {
      // Redirecionar para a página de cadastro com uma mensagem de erro
      header('Location: cadastro.phtml?erro=Os dados informados são inválidos ou já existem!');
    }
  } else if (isset($_POST['login'])) {
    // Obter os dados do formulário de login
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];
    // Verificar se as credenciais estão corretas
    if (login_correto($nome, $senha)) {
      // Iniciar uma sessão e armazenar o nome do usuário na variável $_SESSION['nome']
      session_start();
      $_SESSION['nome'] = $nome;
      // Redirecionar para a página de gerenciamento de usuários
      header('Location: gerenciamento.php');
    } else {
      // Redirecionar para a página de login com uma mensagem de erro
      header('Location: login.php?erro=Nome ou senha incorretos!');
    }
  }
}

// Verificar se o método da solicitação é GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Verificar se a página é de gerenciamento ou de logout
  if (isset($_GET['gerenciamento'])) {
    // Verificar se o usuário está logado
    session_start();
    if (isset($_SESSION['nome'])) {
      // Exibir o nome do usuário e uma lista dos outros usuários cadastrados
      echo "Olá, " . $_SESSION['nome'] . "!<br>";
      echo "Aqui estão os outros usuários cadastrados:<br>";
      // Usar um laço for para percorrer a lista de usuários
      for ($i = 0; $i < count($usuarios); $i++) {
        // Exibir o nome e o e-mail de cada usuário
        echo $usuarios[$i]['nome'] . " - " . $usuarios[$i]['email'] . "<br>";
      }
      // Exibir um link para fazer logout
      echo "<a href='logout.php'>Sair</a>";
    } else {
      // Redirecionar para a página de login com uma mensagem de erro
      header('Location: login.php?erro=Você precisa estar logado para acessar essa página!');
    }
  } else if (isset($_GET['logout'])) {
    // Encerrar a sessão e redirecionar para a página de login com uma mensagem de sucesso
    session_destroy();
    header('Location: login.php?sucesso=Logout realizado com sucesso!');
  }
}
?>
