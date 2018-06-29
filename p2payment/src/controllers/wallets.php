<?php
/**
 * Created by PhpStorm.
 * User: Lekan Adigun
 * Date: 6/19/2018
 * Time: 11:56 PM
 */

use Slim\Http\Request;
use Slim\Http\Response;


/*
 * FlutterWave requires a registered company name,
 * a verification document and other infos that i do not have.
 * Some this endpoints simply takes an amount passed in and credit the user's account wallet
 * */
$app->post("/api/me/wallet/credit", function (Request $request, Response $response) {

    $resp = $this->db->creditWallet($request->getAttribute('user'), $request->getParsedBody()["amount"]);
    return $response->withJson(success($resp));

});

$app->post("/api/wallet/txn/send", function (Request $request, Response $response) {

    $data = $request->getParsedBody();
    $beneficiary = $this->db->getUserByPhone($data["phone"]);
    $user = $request->getAttribute("user");

    $r = $this->db->performTransaction($user, $beneficiary, $data["amount"]);
    return $response->withJson(success($r));
});

$app->get("/api/me/wallet", function (Request $request, Response $response) {

    $user = $request->getAttribute("user");
    $wallet = $this->db->getWallet($user);
    return $response->withJson(success($wallet));
});
?>