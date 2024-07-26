<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;


    public function testEmployeeStore(): void
    {
        //Создание сотрудника
        $response = $this->post('/api/employee', [
           'email' => 'alexey2003@mail.ru',
           'password' => 'alexeypassword'
        ]);
        $response->assertCreated();
        //Проверка на результат работы функции
        $response->assertJsonPath('email','alexey2003@mail.ru');
        //Проверка БД
        $this->assertDatabaseCount('employees',1);
        $this->assertDatabaseHas('employees',[
            'email' => 'alexey2003@mail.ru'
        ]);
        //Проверка на повторное создание на один адрес
        $response = $this->post('/api/employee', [
            'email' => 'alexey2003@mail.ru',
            'password' => 'alexeypassword'
        ]);

        $this->assertEquals('-1', $response->getContent());
    }

    public function testTransactionStore(): void
    {
        //Создание пользователя с айди 1
        $this->post('/api/employee', [
            'email' => 'alexey2003@mail.ru',
            'password' => 'alexeypassword'
        ]);
        //Создание транзакции
        $response = $this->post('/api/transaction', [
            'employee_id' => '1',
            'hours' => '8'
        ]);
        $response->assertCreated();
        $response->assertJsonPath('employee_id','1');

        $this->assertDatabaseCount('transactions','1');

        $this->assertDatabaseHas('transactions',[
            'employee_id' => 1
        ]);

        //Создание транзакции несуществующего пользователя
        $response = $this->post('/api/transaction', [
            'employee_id' => '255',
            'hours' => '8'
        ]);

        $this->assertEquals('-1', $response->getContent());
    }
    public function testTransactionShow(): void
    {
        $response = $this->get('/api/unpaid');
        //Проверка на то, что нет транзакций
        $this->assertEquals('[]', $response->getContent());
        //Создается 10 транзакций
        Transaction::factory(10)->create();
        $response = $this->get('/api/unpaid');
        //Проверка на то, что появились транзакции
        $this->assertNotEquals('[]', $response->getContent());
        $response->assertOk();
    }
    public function testPay(): void
    {
        //Создается 10 транзакций
        Transaction::factory(10)->create();
        //Транзакции погашаются
        $response = $this->patch('/api/pay');
        $response->assertOk();
        //Выводятся неоплаченные транзакции
        $response = $this->get('/api/unpaid');
        $response->assertOk();
        //Проверка на то, что неоплаченных нет
        $this->assertEquals('[]', $response->getContent());
    }
}
