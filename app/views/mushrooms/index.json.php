<?php

$mushroomsToJson = [];

foreach ($mushrooms as $mushroom) {
    $mushroomsToJson[] = ['id' => $mushroom->id, 'scientific_name' => $mushroom->scientific_name];
}

$json['mushrooms'] = $mushroomsToJson;
$json['pagination'] = [
    'page'                       => $paginator->getPage(),
    'per_page'                   => $paginator->perPage(),
    'total_of_pages'             => $paginator->totalOfPages(),
    'total_of_registers'         => $paginator->totalOfRegisters(),
    'total_of_registers_of_page' => $paginator->totalOfRegistersOfPage(),
];
