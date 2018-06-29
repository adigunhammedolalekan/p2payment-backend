<?php
/**
 * Created by PhpStorm.
 * User: Lekan Adigun
 * Date: 3/29/2018
 * Time: 4:00 AM
 */

use Slim\Http\Request;
use Slim\Http\Response;
use Firebase\JWT\JWT;
class JwtAuth {

    public function __invoke(Request $req, Response $res, $next) {

        $noAuths = array('api/login', 'api/user/new', 'api/hello');
        $path = $req->getUri()->getPath();

        if (in_array($path, $noAuths)) {
            $res = $next($req, $res);
            return $res;
        }

        $token = $req->getHeaderLine("Auth");
        if (empty($token)) {
            return $res->withStatus(403)->withJson(error("Invalid auth token"));
        }

        $decoded = "";
        $err = "";
        try {
            $decoded = JWT::decode($token, JWT_SECRET, array('HS256'));
        }catch (Exception $ex) {
            $err = "Malformed token";
        }

        if ($err != "") {
            return $res->withStatus(403)->withJson(error($err));
        }

        $arr = (array) $decoded;
        $req = $req->withAttribute('user', $arr['user']);
        $req = $next($req, $res);
        return $req;
    }
}