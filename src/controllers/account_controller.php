<?php require(CONTROLLER_PATH . "base_controller.php");
class AccountController extends  BaseController
{
    public function __construct()
    {
        $this->folder = 'login';
    }
    public function render($view, $data)
    {
        $view_file = VIEW_PATH . $this->folder . '/' . $view . '.php';
        $template = 'index';
        if(isset($data->template)){
            $template = $data->template;
        }
        if (is_file($view_file)) {
            if (!is_null($data))
                extract($data);
            require_once(TEMPLATE_PATH . $template . '.phtml');
        } else {
            redirect_to(ERROR_URI);
        }
    }
    public function model($model){
        require_once "./src/models/".$model.".php";
        return new $model;
    }
    function index() { 
        header("Location: /index.php?controller=account&action=login");
	}
    function login() { 
        $this->render("login",[
            "template"=>"template_login",
        ]);
	}
    function register() {
        $this->render("register",[
            "template"=>"template_login",
        ]);
	}
    function signIn(){
        try {
            $modelAccount = $this->model("AccountModel");
            $result = $modelAccount->signIn();
            $token = $result->getBody()->getContents(); 
            $_SESSION["token"] = json_decode($token)->token;
            header("Location: ".HOME_URI);
        }catch (\Exception $e) {
            if ($e->hasResponse()){
                if ($e->getResponse()->getStatusCode() == '400') {
                        $_SESSION["errormsLogin"] = "login";
                        header("Location: ".LOGIN_URI);
                        die();
                }
            }
        }
    }
    function logout(){
        $this->checkCustomRequireLogin();
		unset($_SESSION["token"] );
		header("Location: ".LOGIN_URI);
		exit;
	}

    function signUp(){
        try {
            $modelAccount = $this->model("AccountModel");
            $result = $modelAccount->signUp();
            $account = $result->getBody()->getContents(); 
            header("Location: ".LOGIN_URI);
        }catch (\Exception $e) {
            if ($e->hasResponse()){
                if ($e->getResponse()->getStatusCode() == '400') {
                        $_SESSION["errormsg_register"] = "Tài khoản đã tồn tại";
                        header("Location: ".REGISTER_URI);
                        die();
                }
            }
        }
    }
}
?>