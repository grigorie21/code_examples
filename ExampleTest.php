<?php

namespace Tests\Feature;

use app\Services\Sms\Smsru;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    public function testNotAuth()
    {
        $service_owner_user = factory(\app\Models\User::class)->create();
        $feedback_user = factory(\app\Models\User::class)->create();

        $service = factory(\app\Models\Service::class)->states('approved')->create(['user_id' => $feedback_user->id]);

//         coment autorization
//        $this->be($second_user);


        $response = $this->json('POST', '/api/response', ['entity_type' => 'service', 'entity_id' => $service->id, 'text' => 'dddfffggghhh']);

        $response
            ->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Not authorized.'
            ]);
    }

    public function testFeedbackTextTooLong()
    {
        $service_owner_user = factory(\app\Models\User::class)->create();
        $feedback_user = factory(\app\Models\User::class)->create();

        $service = factory(\app\Models\Service::class)->states('approved')->create(['user_id' => $feedback_user->id]);

        $this->be($service_owner_user);

        $text1001 = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean
        massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis,
        ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla
        vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam
        dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate
        eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in,
        viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet.
        Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, t
        ellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Na';

        $response = $this->json('POST', '/api/response', ['entity_type' => 'service', 'entity_id' => $service->id, 'text' => $text1001]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'error',
                'message' => 'Field \'text\' longer than 1000 symbols.'
            ]);
    }

    public function testWrongEntityType()
    {
        $service_owner_user = factory(\app\Models\User::class)->create();
        $feedback_user = factory(\app\Models\User::class)->create();

        $service = factory(\app\Models\Service::class)->states('approved')->create(['user_id' => $service_owner_user]);

        $this->be($feedback_user);


        $response = $this->json('POST', '/api/response', ['entity_type' => 'WrongEntityType', 'entity_id' => $service->id, 'text' => 'dddfffggghhh']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'error',
                'message' => 'Entity_type wrong.',
            ]);

    }

    public function testUserHasAlreadyRespondedToThisService()
    {

        $service_owner_user = factory(\app\Models\User::class)->create();//1
        $feedback_user = factory(\app\Models\User::class)->create();//2


        $service = factory(\app\Models\Service::class)->states('approved')->create(['user_id' => $service_owner_user]);

        factory(\app\Models\Feedback::class)->states('service')->create(['user_id' => $feedback_user->id]);

        $this->be($feedback_user);

        $response = $this->json('POST', '/api/response', ['entity_type' => 'service', 'entity_id' => $service->id, 'text' => 'dddfffggghhh']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'error',
                'message' => 'User has already responded to this service.',
            ]);

    }

    public function testUserHasAlreadyRespondedToThisOrder()
    {

        $order_owner_user = factory(\app\Models\User::class)->create();//1
        $feedback_user = factory(\app\Models\User::class)->create();//2

        $order = factory(\app\Models\Order::class)->states('approved')->create(['user_id' => $order_owner_user]);

        factory(\app\Models\Feedback::class)->states('order')->create(['user_id' => $feedback_user->id]);

        $this->be($feedback_user);

        $response = $this->json('POST', '/api/response', ['entity_type' => 'order', 'entity_id' => $order->id, 'text' => 'dddfffggghhh']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'error',
                'message' => 'User has already responded to this order.',
            ]);

    }

    public function testServiceErrorDatabaseRecord(){
        $service_owner_user = factory(\app\Models\User::class)->create();//1
        $feedback_user = factory(\app\Models\User::class)->create();//2


        $service = factory(\app\Models\Service::class)->states('approved')->create(['user_id' => $service_owner_user]);



        $this->be($feedback_user);

        //try to make faaedback to order that doesnt exists
        $response = $this->json('POST', '/api/response', ['entity_type' => 'order', 'entity_id' => $service->id, 'text' => 'dddfffggghhh']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'error',
                'message' => 'Can\'t record in db.',
            ]);
    }

    public function testOrderErrorDatabaseRecord(){
        $order_owner_user = factory(\app\Models\User::class)->create();//1
        $feedback_user = factory(\app\Models\User::class)->create();//2


        $order = factory(\app\Models\Order::class)->states('approved')->create(['user_id' => $order_owner_user]);



        $this->be($feedback_user);

        //try to make faaedback to order that doesnt exists
        $response = $this->json('POST', '/api/response', ['entity_type' => 'service', 'entity_id' => $order->id, 'text' => 'dddfffggghhh']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'error',
                'message' => 'Can\'t record in db.',
            ]);
    }





    public function testServiceSuccess()
    {
        $service_owner_user = factory(\app\Models\User::class)->create();//1
        $feedback_user = factory(\app\Models\User::class)->create();//2


        $service = factory(\app\Models\Service::class)->states('approved')->create(['user_id' => $service_owner_user]);



        $this->be($feedback_user);

        $response = $this->json('POST', '/api/response', ['entity_type' => 'service', 'entity_id' => $service->id, 'text' => 'dddfffggghhh']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    public function testOrderSuccess()
    {
        $order_owner_user = factory(\app\Models\User::class)->create();//1
        $feedback_user = factory(\app\Models\User::class)->create();//2


        $order = factory(\app\Models\Order::class)->states('approved')->create(['user_id' => $order_owner_user]);



        $this->be($feedback_user);

        $response = $this->json('POST', '/api/response', ['entity_type' => 'order', 'entity_id' => $order->id, 'text' => 'dddfffggghhh']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

}
