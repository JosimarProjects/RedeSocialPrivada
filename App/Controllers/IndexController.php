<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {

		//se o valor estiver setado vamos adicionar a view caso contrario o retorno será vazio

		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '' ;

		$this->render('index');
	}

	
	public function inscreverse() {

	$this->view->erroCadastro = false;
	$this->render('inscreverse');

	}

	public function registrar() {

		//receber dados do formulario
		/*echo "<pre>";
		print_r($_POST);
		echo "</pre>";*/

		$usuario = Container::getModel('Usuario');
		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);

		//criar campo senha no banco com 32 caracteres
		$usuario->__Set('senha', md5($_POST['senha']));

		/*echo "<pre>";	
		print_r($usuario);
		echo "</pre>";*/

		if($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0 ) {
			// count quantidade de elementos contidos se for maior que 0 significa que parametros já existem no banco
			
				$usuario->salvar();
				$this->render('cadastro');

			} else {

				$this->view->usuario = array(
					'nome' => $_POST['nome'],
					'email' => $_POST['email'],
					'senha' => $_POST['senha'],
				);
				$this->view->erroCadastro = true;
				$this->render('inscreverse');	
			}	
		}		

		




}


?>