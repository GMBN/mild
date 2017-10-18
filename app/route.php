<?php

$app = new Base\Route();
$app->get('/notificacao/:nome/:id/cabo', 'Notificacao:teste');
$app->get('/', 'Home:teste');
$app->get('/nome', 'Notificacao:teste');
$app->get('/:perfil', 'Perfil:view');


$app->execute();
