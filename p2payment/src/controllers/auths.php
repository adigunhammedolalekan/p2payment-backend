<?php
/**
 * Created by PhpStorm.
 * User: Lekan Adigun
 * Date: 3/29/2018
 * Time: 5:00 AM
 */

    use Slim\Http\Request;
    use Slim\Http\Response;
    use Firebase\JWT\JWT;

    $app->post('/api/login', function (Request $req, Response $res) {

        $data = $req->getParsedBody();

        if ($data['phone'] == "") {
            $resp = error("phone number is empty");
            return $res->withStatus(400)->withJson($resp);
        }

        $user = $this->db->getUserByPhone($data['phone']);
        if (is_null($user) || empty($user["user_id"])) {
            return $res->withJson(error("User doesn't exists"));
        }

        if (!password_verify($data['password'], $user["password"])) {
            return $res->withStatus(400)->withJson(error("Password is incorrect"));
        }

        $claims = array('user' => $user['user_id']);
        $token = JWT::encode($claims, JWT_SECRET);

        $user["password"] = "";
        $user["token"] = $token;
        $resp = success($user);
        return $res->withStatus(200)->withJson($resp);
    });

    $app->post('/api/user/new', function (Request $request, Response $response) {

        $data = $request->getParsedBody();
        $error = "";

        foreach ($data as $key => $value) {

            if (empty($value)) {
                $error .= $key . " cannot be empty";
            }
        }

        $user = $this->db->getUserByPhone($data['phone']);
        if (!is_null($user) || !empty($user["user_id"])) {
            return $response->withJson(error("Phone number has been taken"));
        }

        if ($error != "") {
            return $response->withJson(error($error));
        }

        $user = $this->db->createUser($data);

        $claims = array('user' => $user['user_id']);
        $token = JWT::encode($claims, JWT_SECRET);

        $user["password"] = "";
        $user["token"] = $token;
        return $response->withJson(success($user));
    });

    $app->get('/api/hello', function (Request $request, Response $response) {
        return $response->withJson(success("Hello, World"));
    })
?>