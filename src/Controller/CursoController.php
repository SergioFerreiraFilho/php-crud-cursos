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
        if (true === empty($_POST)) {
            $this->render('cursos/cadastrar');
            return;
        }

        $curso = new Curso();
        $curso->nome = $_POST['nome'];
        $curso->cargaHoraria = $_POST['cargaHoraria'];
        $curso->categoria = $_POST['categoria'];

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
        $rep = new CursosRepository();
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

    public function relatorio(): void
    {
        $hoje = date('d/m/Y');

        $design = "
            <h1>Relatorio de Alunos</h1>
            <hr>
            <em>Gerado em {$hoje}</em>
        ";

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait'); // tamanho da pagina

        $dompdf->loadHtml($design); //carrega o conteudo do PDF

        $dompdf->render(); //aqui renderiza 
        $dompdf->stream(); //é aqui que a magica acontece
    }
}