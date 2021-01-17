<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

	public function timeline() {

		$this->validaAutenticacao();
		//recuperação dos tweets

		$tweet = Container::getModel('Tweet');
		$tweet->__set('id_usuario', $_SESSION['id']);


		//variaveis de paginação
		$total_registros_pagina = 5;
		//$deslocamento = 0;
		$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
		$deslocamento = ($pagina - 1) * $total_registros_pagina;



		//echo "<br><br><br>Página: $pagina | Total de registros por página: $total_registros_pagina | Deslocamento: $deslocamento";



		$tweets = $tweet->getPorPagina($total_registros_pagina, $deslocamento);
		$total_tweets = $tweet->getTotalRegistros();

		//ceil arredonda para cima
		$this->view->total_de_paginas = $total_de_paginas = ceil($total_tweets['total'] / $total_registros_pagina);
		$this->view->pagina_ativa = $pagina;
		/*echo "<pre>";
			print_r($tweets);
			echo "</pre>";*/
		$this->view->tweets = $tweets;



		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);




		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tweets = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();






		$this->render('timeline');		
	}


	public function tweet() {

		
		
		$this->validaAutenticacao();


		$tweet = Container::getModel('Tweet');
		$tweet->__set('tweet', $_POST['tweet']);
		$tweet->__set('id_usuario', $_SESSION['id']);
		$tweet->salvar();
		header('location: /timeline');		


		}

	public function validaAutenticacao() {

			session_start();

			if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '' ) {
				header('location: /?login=erro');
			} 
		  	

		}	

	public function quemSeguir() {

		$this->validaAutenticacao();

	//	echo "<br><br><br><br><br><br><br>";

		

	
		$pesquisaPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

		//echo 'testando: '.$pesquisaPor;

		$usuarios = array();

		if($pesquisaPor != '') {
			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $pesquisaPor);

			//setendo id para submeter logica para não seguir a sí mesmo
			$usuario->__set('id', $_SESSION['id']);	
			//print_r($usuario);	
			$usuarios = $usuario->getAll(); 

			/*echo "<pre>";
			print_r($usuarios);
			echo "</pre>";*/
		}

		$this->view->usuarios = $usuarios;
		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);




		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tweets = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();


		$this->render('quemSeguir');

	}	

	public function acao(){
		$this->validaAutenticacao();

		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		if ($acao == 'seguir') {
			$usuario->seguirUsuario($id_usuario_seguindo);

			# code...
		}else if ($acao == 'deixar_de_seguir') {
			$usuario->deixarSeguirUsuario($id_usuario_seguindo);
			# code...
		}

		header('location: /quem_seguir');




		


		/*echo "<pre>";
		print_r($_GET);
		echo "</pre>";*/
	
	}


	public function removerTweet() {

		$this->validaAutenticacao();
		$tweet = Container::getModel('Usuario');

		$tweet->__set('id', $_SESSION['id']);
	
		$tweet->removerTweet($_GET['id_tweet']);

		header('location: /timeline');
			





		echo "<pre>";
		print_r($_GET);
		echo "<pre>";
	}


		public function uploadFoto() {

		$this->validaAutenticacao();

		//print_r($_FILES);


		if(isset($_FILES['arquivo'])){
			$arquivo = $_FILES['arquivo'];
			//pathinfo com extension pega a extensão
			$extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
			$usuario = Container::getModel('Usuario');
			$usuario->__set('id', $_SESSION['id']);

			//uniqid cria um nome aleatório
			//$arquivo_nome = md5(uniqid($arquivo['name'])).".".$extensão;
			$arquivo_nome = ($usuario->__get('id').$arquivo['name']);
			//criando local do upload
			$diretorio = "img/users/".$arquivo_nome;

			print_r($diretorio);

			//tmp name é um lugar temporário que o php armazena o arquivo
			move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio);

			//print_r($arquivo_nome);

			
			$usuario->__set('nome', $diretorio);
			$usuario->inserirFoto();
			header('location: /timeline');
			

		};
	//	echo "<br><br><br><br><br><br><br>";

		

	
		
		}
}



?>