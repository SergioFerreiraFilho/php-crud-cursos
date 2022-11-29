<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Curso;
use App\Repository\CursoRepository;
use Dompdf\Dompdf;
use Exception;

class CursoController extends AbstractController
{
    public function listar(): void
    {
        $rep = new CursoRepository();

        $cursos = $rep->buscarTodos();

        $this->render('cursos/listar', [
            'cursos' => $cursos,
        ]);
    }

    public function cadastrar(): void
    {
        
        $rep = new CategoriaRepository();
        if (true === empty($_POST)) {
            $categorias = $rep->buscarTodos();
            $this->render("/curso/cadastrar", ['categorias' => $categorias]);
            return;
        }

        
        if (true === empty($_POST)) {
            $this->render('cursos/cadastrar');
            return;
        }

        $curso = new Curso();
        $curso->nome = $_POST['nome'];
        $curso->categoria = $_POST['categoria'];
        $curso->cargaHoraria = $_POST['cargaHoraria'];

        $rep = new CursoRepository();

        try {
            $rep->inserir($curso);
        } catch (Exception $exception) {
            if (true === str_contains($exception->getMessage(), 'nome')) {
                die('Nome ja existe');
            }


            die('Vish, aconteceu um erro');
        }

        $this->redirect('/cursos/listar');
    }

    public function editar(): void
    {
        $id = $_GET['id'];
        $rep = new CursoRepository();
        $curso = $rep->buscarUm($id);
        $this->render('cursos/editar', [$curso]);
        if (false === empty($_POST)) {
            $curso->nome = $_POST['nome'];
            $curso->cargaHoraria = $_POST['cargaHoraria'];
            $curso->categoria = $_POST['categoria'];
    
            try {
                $rep->atualizar($curso, $id);
            } catch (Exception $exception) {
                if (true === str_contains($exception->getMessage(), 'nome')) {
                    die('Nome ja existe');
                }
    
    
                die('Vish, aconteceu um erro');
            }
            $this->redirect('/cursos/listar');
        }
    }

    public function excluir(): void
    {
        // $this->render('aluno/excluir');
        $id = $_GET['id'];
        $rep = new CursoRepository();
        $rep->excluir($id);
        $this->redirect('/cursos/listar');

    }

    private function redirecionar(iterable $cursos){
        $resultado = '';
        foreach ($cursos as $curso) {
        $resultado .= "
            <tr>
                <td>{$curso->id}</td>
                <td>{$curso->nome}</td>
                <td>{$curso->cargaHoraria}</td>
                <td>{$curso->descricao}</td>
                <td>{$curso->status}</td>
            </tr>";
            }
            return $resultado;
    }

    public function relatorio(): void
    {
        $hoje = date('d/m/Y');
        $curso = $this->repository->buscarTodos();
        $design = "
        <h1>Relatorio de Cursos</h1>
        <hr>
        <em>Gerando em {$hoje}</em>
        <hr>
        <table border='1' width='100%' style='margin-top: 30px;'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Carga Horaria</th>
                    <th>Descrição</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            ".$this->redirecionar($curso)."
            </tbody>
        </table>
        ";

        $dompdf = new Dompdf();
        $dompdf->loadHtml(($design)); 
        $dompdf->setPaper('A4', 'portrait'); 
        $dompdf->render();
        $dompdf->stream('Relatorio-Cursos.pdf', ['Attachment' => 0]); 
    }
}