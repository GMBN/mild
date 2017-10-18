<?php

$app = new Base\Route();

$app->post('/notificacao/:nome/:id/site', 'Notificacao:teste');
$app->get('/', 'Home:teste');
$app->ajax('/nome', 'Notificacao:teste');
$app->get('/:perfil', 'Perfil:view');


$app->execute();
