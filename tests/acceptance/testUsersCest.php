<?php

use Helper\Acceptance;

class testUsers
{
    public const NUMBER = 10;    
    public $data;
    public $users;

    public function _before(AcceptanceTester $I)
    {
        /**
         * creating 1o users then checking if they exist in db
         */
        for($i = 1; $i<=self::NUMBER; $i++) 
        {
            $faker = $I->getFaker();
    
            $this->data = [
                "job" => $faker->jobTitle,
                "superhero" => $faker->boolean(),
                "skill" => $faker->word,
                "email" => $faker->email,
                "name" => $faker->name,
                "DOB" => $faker->date("Y-m-d"),
                "avatar" => $faker->imageUrl(),
                "canBeKilledBySnap" => $faker->boolean(),
                "created_at" => $faker->date("Y-m-d"),
                "owner" => 'magzhantelegram',
                ];
            $I->haveInCollection('people', $this->data);
            $I->seeInCollection('people', $this->data);
            // $I->seeNumElementsInCollection('people', 10, ['owner'=>$this->data['owner']]);
            $user = $I -> grabFromCollection('people', $this->data);
            array_push($this->users, $user);
        }
    }

    // /**
    //  * checks getting users by 'owner'
    //  */
    // public function checkGetManyPeople(AcceptanceTester $I)
    // {
    //     unset($this->data['canBeKilledBySnap'], $this->data['created_at']);

    //     $I->wantTo('check created users');
    //     $I->sendGet('/people', ['owner'=>$this->data['owner']]);
    //     $I->seeResponseContainsJson([$this->data]);
    // } 

    /**
     * Checking for the presence of users, after delete with the value killedBySnap
     * 
     * @group t2
     */
    public function snapKillUsersTest(AcceptanceTester $I)
    {
       
        $I->amOnPage('/?owner=magzhantelegram');
        
        /**
         * checking each user displayed
         */
        foreach($this->users as $value) {
            $I->see($value ['name']);
        }  

        $I->click('//*[@id="snap"]');
        $I->wait(5);
        /**
         * creating 2 variables to save data of users who should be deleted or shouldn't be deleted
         */
        $usersTrueSnap = array(); 
        $usersFalseSnap = array();
        /**
         * sorting users who should be deleted and who shouldn't be
         */
        foreach($this->users as $bySnap) {
        if ($bySnap['canBeKilledBySnap']== true)
         {
            array_push($this->usersTrueSnap, $bySnap);
         }  else  {
            array_push($this->usersFalseSnap, $bySnap);
         }
        }

        /**
         * checking that users which should be deleted - were deleted
         */
        foreach($this->usersTrueSnap as $value) {
        $I->dontSee($value ['name']);
        }
        
        /**
         * checking that users which shouldn't be deleted - weren't deleted
         */
        foreach($this->usersFalseSnap as $value) {
            $I->see($value ['name']);
        }



        /**
         * checking if the users were deleted
         */
        $I->dontSeeInCollection('people', $this->usersTrueSnap);}
}