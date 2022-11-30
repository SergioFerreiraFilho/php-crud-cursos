<?php

declare(strict_types=1);

namespace App\Model;

class Curso
{
    public string $nome;
    public string $cargaHoraria;
    public string $descricao;
    public int $status;
    public int $categoria_id;
}