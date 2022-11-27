<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Categoria;

use App\Repository\CategoriaRepository;

class CategoriaController extends AbstractController
{
    public function listar(): void
    {
        $rep = new CategoriaRepository();

        $categorias = $rep->buscarTodos();

        $this->render('categoria/listar', [
            'categorias' => $categorias,
        ]);
    }

    public function cadastrar(): void
    {
        echo "Pagina de cadastrar";
    }

    public function excluir(): void
    {
        echo "Pagina de excluir";
    }

    public function editar(): void
    {
        echo "Pagina de editar";
    }
}