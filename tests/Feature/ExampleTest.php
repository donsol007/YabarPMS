<?php

it('redirects guests to the login screen', function () {
    $this->get('/')
        ->assertRedirect('/login');
});