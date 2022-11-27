<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProfessorRepository;

use Dompdf\Dompdf;

class ProfessorController extends AbstractController
{
    // public const REPOSITORY = new ProfessorRepository();

    public function listar(): void
    {
        $rep = new ProfessorRepository();
        $professores = $rep->buscarTodos();

        $this->render("professor/listar", [
            'professores' => $professores,
        ]);
    }

    public function cadastrar(): void
    {
        echo "Pagina de cadastrar";
    }

    public function excluir(): void
    {
        $id = $_GET['id'];
        $rep = new ProfessorRepository();
        $rep->excluir($id);
        $this->redirect("/professores/listar");
    }

    public function editar(): void
    {
        
    }

    public function relatorio(): void
    {
        $hoje = date('d/m/Y');

        $design = "
            <h1>Relatorio de Professores</h1>
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