<?php
/**
 * Created by PhpStorm.
 * User: bobuchacha
 * Date: 7/11/18
 * Time: 1:48 AM
 */

Class SimpleRouter{
	protected $gets;
	protected $posts;
	protected $path;
	protected $queryString;
	protected $method;
	protected $headers;

	public function __construct() {
		$this->gets = [];
		$this->posts = [];
		$this->path = $_SERVER["PATH_INFO"] ?? "/";
		$this->queryString = $_SERVER["QUERY_STRING"] ?? "";
		$this->method = $_SERVER["REQUEST_METHOD"];
		$this->headers = getallheaders();

		// check for Access Token exists
		if (!key_exists('Access-Token', $this->headers) && $this->path != '/avatar') {
			?>
			<html><head><title>404 - Not found</title><style>
					body {  font-family:arial;  }

					#fof{display:block; width:100%; padding:150px 0; text-align:center;}
					#fof .hgroup {display:block; width:80%; margin:0 auto; padding:0;}
					.clear{clear:both}
					#fof .hgroup h1, #fof .hgroup h2{margin:0 0 0 40px; padding:0; float:left; text-transform:uppercase;}
					#fof .hgroup h1{margin-top:-90px; font-size:200px;}
					#fof .hgroup h2{font-size:60px;}
					#fof .hgroup h2 span{display:block; font-size:30px;}

				</style></head><body>
			<div class="wrapper row2">
  <div id="container" class="clear">
    <section id="fof" class="clear">
      <div class="hgroup clear">
        <h1>404</h1>
        <h2>Error ! <span>Page Not Found</span></h2>
      </div>

	    <p class="clear">For Some Reason The Page You Requested Could Not Be Found On Our Server.<br/><a href="/">Go Home &raquo;</a></p>


    </section>

  </div>
</div>
			</body></html>
			<?php
			die();
		}
	}

	public function get($uri, $callback){
		$this->gets[$uri] = $callback;
	}
	public function post($uri, $callback){
		$this->posts[$uri] = $callback;
	}
	public function run(){
		if ($this->method=='GET' && key_exists($this->path, $this->gets)) {
			$this->gets[$this->path]();
		}elseif ($this->method=='POST' && key_exists($this->path, $this->posts)) {
			$this->posts[$this->path]();
		}else{
			$this->response([
				                'error' => true,
				                'message' => 'Invalid request ' . $this->path
			                ]);

		}
	}
	public function request($n){
		global $_REQUEST;
		return isset($_REQUEST[$n]) ? $_REQUEST[$n] : '';
	}
	public function request_get($n){
		global $_GET;
		return isset($_GET[$n]) ? $_GET[$n] : '';
	}
	public function request_post($n){
		global $_POST;
		return isset($_POST[$n]) ? $_POST[$n] : '';
	}

	public function response($r){
		echo json_encode($r);
		die();
	}
	public function get_header($name){
		return key_exists($name, $this->headers)
			? $this->headers[$name]
			: '';
	}
}