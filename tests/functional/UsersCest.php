<?php

use Codeception\Example;
use Faker\Factory;
use Faker\Guesser\Name;

/**
 * класс для работы с пользователем
 */
class UsersCest
{
    /**
     * test for creating user
     * @group test1
     */
    public function checkUserCreate(FunctionalTester $I)
    {
        $userData = [
            'email' => 'M11aga@kolesa.kz',
            'owner' => 'M11aga',
            'job'   => 'M11ercedes',
            'name'  => 'M11agomed',
        ];
        /**creating user */
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('human', $userData);
        /**Checks that the response code is 2xx: 200 - Ok (хорошо), 201 - Created (создано) */
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseContainsJson(array('status' => 'ok'));
        /**Проверяем, что юзер действительно создался и данные совпадают тем, которые мы генерировали*/
        $I->sendGet('people', $userData);
        $I->seeResponseMatchesJsonType([
        "job"          => 'string',
        "_id"          => 'string',
        "email"        => 'string',
        "superhero"    => 'boolean',
        "name"         => 'string',
        "owner"        => 'string']);
        $user_id = $I->grabResponse();
        $id = $I->grabDataFromResponseByJsonPath('http://api.izze.xyz/test/people?_id=$id');
        $I->sendPut('http://api.izze.xyz/test/people?_id=$id', ['name' => 'MMM']);
        $I->seeResponseCodeIsSuccessful();
        $I->sendDelete('http://api.izze.xyz/test/people?_id=$id');
    }

    /**
     * checking negative cases for creating user with no email
     * @group test2
     * @dataProvider getNegativeCases
     * @param Example $data
     */

    public function checkCreateNegative(FunctionalTester $I, Example $data)
    {
        $faker = Faker\Factory::create('kk_KZ');
        
        $email = $faker -> email;
        $owner = $faker -> userName;

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('human', [
            $data['email'] ? $email : null,
            $data['owner'] ? $owner : null,
        ]);
        $I->seeResponseContainsJson($data['errorCase']);
    }
    /**
     * dataProvider for checkCreateNegative test
     */
    protected function getNegativeCases()
    {
        return[
                [
                    'email'=>true,
                    'owner'=>false,
                    'errorCase'=>['message' => 'email не передан']
                ],
                [
                    'email'=>false,
                    'owner'=>true,
                    'errorCase'=>['message' => 'email не передан']
                ]
        ];
    }
}
