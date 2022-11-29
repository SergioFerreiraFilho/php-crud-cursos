<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Aluno;
use App\Repository\AlunoRepository;
use Dompdf\Dompdf;
use Exception;

class AlunoController extends AbstractController
{
    public function listar(): void
    {
        $rep = new AlunoRepository();

        $alunos = $rep->buscarTodos();

        $this->render('aluno/listar', [
            'alunos' => $alunos,
        ]);
    }

    public function cadastrar(): void
    {
        if (true === empty($_POST)) {
            $this->render('aluno/cadastrar');
            return;
        }

        $aluno = new Aluno();
        $aluno->nome = $_POST['nome'];
        $aluno->dataNascimento = $_POST['nascimento'];
        $aluno->cpf = $_POST['cpf'];
        $aluno->email = $_POST['email'];
        $aluno->genero = $_POST['genero'];

        $rep = new AlunoRepository();

        try {
            $rep->inserir($aluno);
        } catch (Exception $exception) {
            if (true === str_contains($exception->getMessage(), 'cpf')) {
                die('CPF ja existe');
            }

            if (true === str_contains($exception->getMessage(), 'email')) {
                die('Email ja existe');
            }

            die('Vish, aconteceu um erro');
        }

        $this->redirect('/alunos/listar');
    }

    public function editar(): void
    {
        $id = $_GET['id'];
        $rep = new AlunoRepository();
        $aluno = $rep->buscarUm($id);
        $this->render('aluno/editar', [$aluno]);
        if (false === empty($_POST)) {
            $aluno->nome = $_POST['nome'];
            $aluno->dataNascimento = $_POST['nascimento'];
            $aluno->cpf = $_POST['cpf'];
            $aluno->email = $_POST['email'];
            $aluno->genero = $_POST['genero'];
    
            try {
                $rep->atualizar($aluno, $id);
            } catch (Exception $exception) {
                if (true === str_contains($exception->getMessage(), 'cpf')) {
                    die('CPF ja existe');
                }
    
                if (true === str_contains($exception->getMessage(), 'email')) {
                    die('Email ja existe');
                }
    
                die('Vish, aconteceu um erro');
            }
            $this->redirect('/alunos/listar');
        }
    }

    public function excluir(): void
    {
        // $this->render('aluno/excluir');
        $id = $_GET['id'];
        $rep = new AlunoRepository();
        $rep->excluir($id);
        $this->redirect('/alunos/listar');

    }

    private function redirecionar(iterable $alunos)
    {
        $resultado = '';
        foreach ($alunos as $aluno) {
            $resultado .= "
            <tr>
                <td>{$aluno->id}</td>
                <td>{$aluno->nome}</td>
                <td>{$aluno->matricula}</td>
                <td>{$aluno->email}</td>
                <td>{$aluno->status}</td>
                <td>{$aluno->genero}</td>
                <td>{$aluno->dataNascimento}</td>
                <td>{$aluno->cpf}</td>
            </tr>";
        }
        return $resultado;
    }

    public function relatorio(): void
    {
        $hoje = date('d/m/Y');
        $aluno = $this->repository->buscarTodos();
        $desing = "
        <h1>Relatorio de Alunos</h1>
        <hr>
        <em>Gerando em {$hoje}</em>
        <hr>
        <table border='1' width='100%' style='margin-top: 30px;'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Matricula</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Gênero</th>
                    <th>Data Nascimento</th>
                    <th>CPF</th>
                </tr>
            </thead>
            <tbody>
            " . $this->redirecionar($aluno) . "
            </tbody>
        </table>
        ";

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait'); // tamanho da pagina

        $dompdf->loadHtml($design); //carrega o conteudo do PDF

        $dompdf->render(); //aqui renderiza 
        $dompdf->stream(); //é aqui que a magica acontece
    }
}