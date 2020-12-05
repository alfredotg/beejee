<?php

namespace BeeJee;

use Illuminate\Database\Capsule\Manager as Capsule;
use BeeJee\Model\User;

class Migrations extends Injectable
{
    public function run()
    {
        $schema = $this->di->get(Capsule::class)->schema();
        if (!$schema->hasTable('users')) {
            $schema->create('users', function ($table) {
                $table->increments('id');
                $table->string('login')->unique();
                $table->string('password');
            });
        }
        if (!$schema->hasTable('tasks')) {
            $schema->create('tasks', function ($table) {
                $table->increments('id');
                $table->string('username');
                $table->string('email');
                $table->string('description');
                $table->integer('status');
                $table->integer('description_changed')->default(0);
            });
        }

        if (!User::where('login', '=', 'admin')->count()) {
            $user = new User;
            $user->login = 'admin';
            $user->setPassword('123');
            $user->save();
        }
    }
}
