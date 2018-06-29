<?php
/**
 * Created by PhpStorm.
 * User: Lekan Adigun
 * Date: 3/29/2018
 * Time: 5:02 AM
 */

class User
{

    public $email;
    public $firstname;
    public $surname;
    public $lastname;
    public $password;
    public $card;
    public $last4;
    public $timeJoined;

    public static function fromUser($data) {

        $u = new User();
        $u->email = $data['email'];
        $u->surname = $data['surname'];
        $u->firstname = $data['firstname'];
        $u->lastname = $data['lastname'];
        $u->password = $data['password'];
        $u->card = $data['card'];
        $u->last4 = $data['last4'];
        $u->timeJoined = $data['dateJoined'];

        return $u;
    }
}