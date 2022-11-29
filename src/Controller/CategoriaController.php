<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Categoria;

use App\Repository\CategoriaRepository;

use Exception;

use Dompdf\Dompdf;

class CategoriaController extends AbstractController
{
    private CategoriaRepository $repository;
    public function __construct()
    {
        $this->repository = new CategoriaRepository();
    }
    
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
        if (true === empty($_POST)) {
            $this->render('categoria/cadastrar');
            return;
        }

        $categoria = new Categoria();
        $categoria->nome = $_POST['nome'];

        $rep = new CategoriaRepository();

        try {
            $rep->inserir($categoria);
        } catch (Exception $exception) {
            if (true === str_contains($exception->getMessage(), 'nome')) {
                die('Nome já existe');
            }
            die('Vish, aconteceu um erro');
        }

        $this->redirect('/categorias/listar');
    }

    public function excluir(): void
    {
        $id = $_GET['id'];
        $rep = new CategoriaRepository();
        $rep->excluir($id);
        $this->redirect("/categorias/listar");
    }

    public function editar(): void
    {
        $id = $_GET['id'];
        $rep = new CategoriaRepository();
        $categoria = $rep->buscarUm($id);
        $this->render('Categoria/editar', [$categoria]);
        if (false === empty($_POST)) {
            $categoria->nome = $_POST['nome'];
    
            try {
                $rep->atualizar($categoria, $id);
            } catch (Exception $exception) {
                if (true === str_contains($exception->getMessage(), 'nome')) {
                    die('Nome ja existe');
                }
    
    
                die('Vish, aconteceu um erro');
            }
            $this->redirect('/categorias/listar');
        }
    }
    private function renderizar(iterable $categorias)
    {
        $resultado = '';
        foreach($categorias as $categoria){
            $resultado .= "
            <tr>
                <td>{$categoria->id}</td>
                <td>{$categoria->nome}</td>
            </tr>";
        }
        return $resultado;
    }
    public function relatorio(): void
    {
        $hoje = date('d/m/Y');
        $categorias = $this->repository->buscarTodos();
        $design =  "
            <h1>Relatorio de Alunos</h1>
            <hr>
            <em>Gerado em {$hoje}</em>
            <br>
            <table border='1' width='100%' style='margin-top: 30px;'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                    </tr>
                </thead>
                <tbody>
                ".$this->renderizar($categorias)."
                </tbody>
            </table>
        ";
        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($design);
        $dompdf->render();
        $dompdf->stream('relatorio-categoria.pdf', ['Attachment'=> 0,]);
    }
}