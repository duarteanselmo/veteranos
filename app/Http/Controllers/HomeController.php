<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Jogador;
use App\Models\JogadorGol;
use App\Models\Temporadas;
use App\Models\Equipe;
use App\Models\Cartao;
use App\Models\JogadorCartoes;
use Barryvdh\DomPDF\Facade\Pdf;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function getDados(Request $request) {
        $obj = (object)[];
        $obj->jogadores = $this->getJogadores($request);
        $obj->temporadas = $this->getTemporadas();
        $obj->equipes = $this->getEquipes();
        $obj->cartoes = $this->getCartoes();
        return $obj;
    }

    public function salvarJogador(Request $request)
    {
        $jogador = new Jogador();
        $jogador->nome = $request->nome;
        $jogador->save();
        return $this->getJogadores($request);
    }

    public function atualizarJogador(Request $request){
        $jogador = Jogador::find($request->id);
        $jogador->nome = $request->nome;
        $jogador->update();
        return $this->getJogadores($request);
    }

    public function getJogadores(Request $request)
    {
        // $jogadores = Jogador::get();
        $jogadores = $this->getJogadoresPorTemporada($request->temporada_id);
        foreach ($jogadores as $jogador) {
            $jogador_estatistica = $this->getJogadorEstatisticas($jogador->id, $request->temporada_id);
            $jogador->gols = $jogador_estatistica->gols;
            $jogador->gols_sofridos = $jogador_estatistica->gols_sofridos;
            $jogador->gol_contra = $jogador_estatistica->gol_contra;
            $jogador->participacoes = $this->verificaParticipacao($jogador->id);
            $jogador->cartao_amarelo = $this->getCartaoAmarelo($jogador->id);
            $jogador->cartao_vermelho = $this->getCartaoVermelho($jogador->id);
        }
        
        return $jogadores->sortByDesc('gols')->values();
    }

    function verificaParticipacao($jogador_id) {
        $jogador_gols = JogadorGol::where('jogador_id', $jogador_id)->count();
        return $jogador_gols;
    }

    public function getJogadorEstatisticas($jogador_id, $temporada_id) {
        $jogador_gols = JogadorGol::select(DB::raw('count(id) as participacoes, sum(gols) as gols, sum(gols_sofridos) as gols_sofridos, sum(gol_contra) as gol_contra'))
        ->where('jogador_id', $jogador_id);

        if($temporada_id)
            $jogador_gols = $jogador_gols->where('temporada_id', $temporada_id);

        $jogador_gols = $jogador_gols->first();

        return $jogador_gols;
    }

    public function getTemporadas() {
        $temporadas = Temporadas::select('id', 'nome')->get();
        return $temporadas;
    }

        public function getTemporadaPorId($temporada_id) {
        $temporada = Temporadas::select('nome')->where('id', $temporada_id)->first();
        return $temporada->nome;
    }

    public function getEquipes()
    {
        $equipes = Equipe::get();
        return $equipes;
    }

    public function getGolsJogador(Request $request)
    {
        $jogador_gols = JogadorGol::select(DB::raw(
            'j.nome, jogador_gols.gols, jogador_gols.gols_sofridos,DATE_FORMAT(jogador_gols.data, "%Y-%m-%d") as data,jogador_gols.jogador_id,jogador_gols.id,jogador_gols.equipe_id,e.nome As equipe, jogador_gols.temporada_id'))
        ->join('jogadores As j', 'j.id', '=', 'jogador_gols.jogador_id')
        ->leftJoin('equipes As e', 'e.id', '=', 'jogador_gols.equipe_id')
        ->where('jogador_id', $request->jogadorId);

        if($request->temporada_id)
            $jogador_gols = $jogador_gols->where('temporada_id', $request->temporada_id);

        $jogador_gols = $jogador_gols->get();
        return $jogador_gols;
    }

    public function storeGol(Request $request)
    {
        $gols = new JogadorGol();
        $gols->jogador_id = $request->jogadorId;
        $gols->equipe_id = $request->equipe_id;
        $gols->gols = $request->gols;
        $gols->gols_sofridos = $request->gols_sofridos;
        $gols->data = $request->data;
        $gols->temporada_id = $request->temporada_id;
        $gols->save();

        $data = new class{};
        $data->jogador_gols = $this->getGolsJogador($request);
        $data->jogadores = $this->getJogadores($request);
        return response()->json($data);

    }

    public function updateGol(Request $request)
    {
        $jogadorGol = JogadorGol::find($request->id);
        if($request->gols != '')
            $jogadorGol->gols = $request->gols;
        if($request->gols_sofridos != '')
            $jogadorGol->gols_sofridos = $request->gols_sofridos;
        $jogadorGol->equipe_id = $request->equipe_id;
        $jogadorGol->data = $request->data;
        $jogadorGol->temporada_id = $request->temporada_id;
        $jogadorGol->update();

        $data = new class{};
        $data->jogador_gols = $this->getGolsJogador($request);
        $data->jogadores = $this->getJogadores($request);
        return response()->json($data);

    }

    public function storeCartao(Request $request)
    {
        // dd($request);
        if($request->jogador_cartao_id)
            $cartao = JogadorCartoes::find($request->jogador_cartao_id);
        else
            $cartao = new JogadorCartoes();

        $cartao->jogador_id = $request->jogador_id;
        $cartao->equipe_id = $request->equipe_id;
        $cartao->cartao_id = $request->cartao_id;
        $cartao->quantidade = $request->qtd_cartao;
        $cartao->data = $request->data_cartao;
        $cartao->temporada_id = $request->temporada_id;
        $request->jogador_cartao_id ? $cartao->update() : $cartao->save();

        $data = new class{};
        $data->jogador_cartoes = $this->getCartoesJogador($request);
        $data->jogadores = $this->getJogadores($request);
        return response()->json($data);
    }

    public function getEstatisticas(Request $request)
    {
        $jogador_gols = JogadorGol::select(DB::raw('day(data) as dia, month(data) as mes , year(data) as ano, temporada_id'))->join('equipes As e', 'e.id', '=', 'jogador_gols.equipe_id')
        ->whereIn('e.id',[3,4]);

        if($request->temporada_id)
            $jogador_gols = $jogador_gols->where('temporada_id', $request->temporada_id);

        $jogador_gols = $jogador_gols->groupBy('dia','mes','ano', 'temporada_id')->get();

        $data = new class{};
        $data->vitoriasTimeAzul = $this->vitoriasTimeAzul($jogador_gols);
        $data->derrotasTimeAzul = $this->derrotasTimeAzul($jogador_gols);
        $data->vitoriasTimePreto = $this->vitoriasTimePreto($jogador_gols);
        $data->derrotasTimePreto = $this->derrotasTimePreto($jogador_gols);
        $data->empates = $this->empates($jogador_gols);
        $data->golsTimeAzul = $this->golsTimeAzul($request->temporada_id);
        $data->golsTimePreto = $this->golsTimePreto($request->temporada_id);
        $data->golsTotal = $data->golsTimeAzul+$data->golsTimePreto;
        return response()->json($data);
    }

    public function getEstatisticasPdf(Request $request)
    {
        $jogador_gols = JogadorGol::select(DB::raw('day(data) as dia, month(data) as mes , year(data) as ano, temporada_id'))->join('equipes As e', 'e.id', '=', 'jogador_gols.equipe_id')
        ->whereIn('e.id',[3,4]);

        if($request->temporada_id)
            $jogador_gols = $jogador_gols->where('temporada_id', $request->temporada_id);

        $jogador_gols = $jogador_gols->groupBy('dia','mes','ano', 'temporada_id')->get();

        $data = new class{};
        $data->vitoriasTimeAzul = $this->vitoriasTimeAzul($jogador_gols);
        $data->derrotasTimeAzul = $this->derrotasTimeAzul($jogador_gols);
        $data->vitoriasTimePreto = $this->vitoriasTimePreto($jogador_gols);
        $data->derrotasTimePreto = $this->derrotasTimePreto($jogador_gols);
        $data->empates = $this->empates($jogador_gols);
        $data->golsTimeAzul = $this->golsTimeAzul($request->temporada_id);
        $data->golsTimePreto = $this->golsTimePreto($request->temporada_id);
        $data->golsTotal = $data->golsTimeAzul+$data->golsTimePreto;
        return $data;
    }

    public function gols($dia, $mes, $ano, $equipe_id, $temporada_id)
    {
        
        return $jogador_gols = JogadorGol::select(DB::raw('sum(gols)'))
        ->whereRaw('day(data) = '.$dia)
        ->whereRaw('month(data) = '.$mes)
        ->whereRaw('year(data) = '.$ano)
        ->where('equipe_id', $equipe_id)
        ->where('temporada_id', $temporada_id)
        ->first();
    }

    public function vitoriasTimeAzul($jogador_gols)
    {
        $vitorias = 0;
        foreach ($jogador_gols as $value) {

            $azul = $this->gols($value->dia, $value->mes, $value->ano, 3, $value->temporada_id);
            $preto = $this->gols($value->dia, $value->mes, $value->ano, 4, $value->temporada_id);
            if($azul>$preto)
                $vitorias++;
        }
        return $vitorias;
    }

    public function derrotasTimeAzul($jogador_gols)
    {
        $derrotas = 0;
        foreach ($jogador_gols as $value) {

            $azul = $this->gols($value->dia, $value->mes, $value->ano, 3, $value->temporada_id);
            $preto = $this->gols($value->dia, $value->mes, $value->ano, 4, $value->temporada_id);
            if($azul<$preto)
                $derrotas++;
        }
        return $derrotas;
    }

    public function empates($jogador_gols)
    {
        $empates = 0;
        foreach ($jogador_gols as $value) {

            $azul = $this->gols($value->dia, $value->mes, $value->ano, 3, $value->temporada_id);
            $preto = $this->gols($value->dia, $value->mes, $value->ano, 4, $value->temporada_id);
            if($azul==$preto)
                $empates++;
        }
        return $empates;
    }

    public function vitoriasTimePreto($jogador_gols)
    {
        $vitorias = 0;
        foreach ($jogador_gols as $value) {

            $azul = $this->gols($value->dia, $value->mes, $value->ano, 3, $value->temporada_id);
            $preto = $this->gols($value->dia, $value->mes, $value->ano, 4, $value->temporada_id);
            if($azul<$preto)
                $vitorias++;
        }
        return $vitorias;
    }

    public function derrotasTimePreto($jogador_gols)
    {
        $derrotas = 0;
        foreach ($jogador_gols as $value) {

            $azul = $this->gols($value->dia, $value->mes, $value->ano, 3, $value->temporada_id);
            $preto = $this->gols($value->dia, $value->mes, $value->ano, 4, $value->temporada_id);
            if($azul>$preto)
                $derrotas++;
        }
        return $derrotas;
    }

    public function golsTimeAzul($temporada_id)
    {
        $gols = 0;
        $quauntidadeGolsMarcados = JogadorGol::select(DB::raw('sum(gols) as gols'))
        ->where('equipe_id', 3);
        if($temporada_id)
            $quauntidadeGolsMarcados = $quauntidadeGolsMarcados->where('temporada_id', $temporada_id);

        $quauntidadeGolsMarcados = $quauntidadeGolsMarcados->first();

        $quauntidadeGolsContraMarcados = JogadorGol::select(DB::raw('sum(gol_contra) as gol_contra'))
        ->where('equipe_id', 4);
        if($temporada_id)
            $quauntidadeGolsContraMarcados = $quauntidadeGolsContraMarcados->where('temporada_id', $temporada_id);

        $quauntidadeGolsContraMarcados = $quauntidadeGolsContraMarcados->first();

        if($quauntidadeGolsContraMarcados)
            $gols = $quauntidadeGolsMarcados->gols+$quauntidadeGolsContraMarcados->gol_contra;
        else
            $gols = $quauntidadeGolsMarcados->gols;
        return $gols;
    }

    public function golsTimePreto($temporada_id)
    {
        $gols = 0;
        $quauntidadeGolsMarcados = JogadorGol::select(DB::raw('sum(gols) as gols'))
        ->where('equipe_id', 4);
        if($temporada_id)
            $quauntidadeGolsMarcados = $quauntidadeGolsMarcados->where('temporada_id', $temporada_id);

        $quauntidadeGolsMarcados = $quauntidadeGolsMarcados->first();

        $quauntidadeGolsContraMarcados = JogadorGol::select(DB::raw('sum(gol_contra) as gol_contra'))
        ->where('equipe_id', 3);
        if($temporada_id)
            $quauntidadeGolsContraMarcados = $quauntidadeGolsContraMarcados->where('temporada_id', $temporada_id);

        $quauntidadeGolsContraMarcados = $quauntidadeGolsContraMarcados->first();
        
        if($quauntidadeGolsContraMarcados)
            $gols = $quauntidadeGolsMarcados->gols+$quauntidadeGolsContraMarcados->gol_contra;
        else
            $gols = $quauntidadeGolsMarcados->gols;
        return $gols;
    }

    public function deleteJogador(Request $request)
    {
        $jogador = Jogador::find($request->jogadorId);

        $jogador_gols = JogadorGol::where('jogador_id', $request->jogadorId);
        if($jogador_gols->get())
            $jogador_gols->delete();
        
        $jogador->delete();

        return $this->getJogadores($request);
    }

    function deleteGols(Request $request) {
        $jogador_gols = JogadorGol::find($request->jogadorGolId);
        $jogador_gols->delete();

        $data = new class{};
        $data->jogador_gols = $this->getGolsJogador($request);
        $data->jogadores = $this->getJogadores($request);
        return response()->json($data);
    }

    public function getJogadoresPorTemporada($temporada_id)
    {
        $jogadores = Jogador::select('jogadores.id', 'jogadores.nome')
        ->join('jogador_gols As jg', 'jg.jogador_id', '=', 'jogadores.id');

        if($temporada_id)
            $jogadores = $jogadores->where('jg.temporada_id', $temporada_id);

        $jogadores = $jogadores->groupBy('jogadores.id')
        ->get();
        return $jogadores;
    }

    public function gerarPdf(Request $request)
    {
        $listaGols = $this->getJogadores($request);
        $estatisticas = $this->getEstatisticasPdf($request);
        $estatisticasJogos = $this->getEstatisticasJogos($request);
        $temporada = $this->getTemporadaPorId($request->temporada_id);

        $html = view('listaGolsPdf',
		[
			'listaGols' => $listaGols,
			'estatisticas' => $estatisticas,
            'estatisticasJogos' => $estatisticasJogos,
            'temporada' => $temporada
		])->render();
        return PDF::loadHTML($html)->setPaper('a4','landscape')->stream('Lista de Gols');
    }
    public function golsJogadorPdf(Request $request)
    {
        $jogador = Jogador::find($request->jogadorId);
        $temporada = Temporadas::find($request->temporada_id);

        $jogadorGols = $this->getGolsJogador($request);
        $somaGolsPorTime = $this->somaGolsPorTime($jogadorGols);

        foreach ($jogadorGols as $value) {
            $value->data_formatada = date('d/m/Y',strtotime($value->data));
        }

        $html = view('golsJogadorPdf',
		[
			'jogadorGols' => $jogadorGols,
            'somaGolsPorTime' => $somaGolsPorTime,
            'jogador' => $jogador->nome,
            'temporada' => $temporada->nome            
		])->render();
        return PDF::loadHTML($html)->setPaper('a4','landscape')->stream('Lista de Gols');
    }

    public function somaGolsPorTime($jogadorGols)
    {
        $golsTimePreto = 0;
        $golsTimeAzul = 0;
        $golsTotal = 0;
        if($jogadorGols){
            foreach ($jogadorGols as $value) {
                $golsTotal += $value->gols;
                if($value->equipe_id == 3)
                    $golsTimeAzul += $value->gols;
                if($value->equipe_id == 4)
                    $golsTimePreto += $value->gols;
            }
        }
        $obj = (object)[];
        $obj->golsTimePreto = $golsTimePreto;
        $obj->golsTimeAzul = $golsTimeAzul;
        $obj->golsTotal = $golsTotal;
        return $obj;
    }


    public function getEstatisticasJogos(Request $request)
    {
        $datas_jogos = JogadorGol::select('data');
        if($request->temporada_id)
            $datas_jogos = $datas_jogos->where('temporada_id', $request->temporada_id);

        $datas_jogos = $datas_jogos->groupBy('data')->orderBy('data', 'desc')->get();

        foreach ($datas_jogos as $data) {
            $data->golsTimePreto = (int)$this->getGolsTimePorDia($data->data, 4);
            $data->golsTimeAzul = (int)$this->getGolsTimePorDia($data->data, 3);
            $data->data_formatada = date('d/m/Y',strtotime($data->data));
        }
        return $datas_jogos;
    }

    public function getGolsTimePorDia($data, $equipe_id)
    {
        $gols = JogadorGol::select('gols')->where('data', $data)->where('equipe_id', $equipe_id)->groupBy('data')->sum('gols');
        $quauntidadeGolsContraMarcados = $this->getGolsContra($data, $equipe_id);
        if($quauntidadeGolsContraMarcados)
            return $gols+$quauntidadeGolsContraMarcados;
        
        return $gols;
    }

    public function getGolsContra($data, $equipe_id)
    {
        $quauntidadeGolsContraMarcados = JogadorGol::select('gol_contra')
        ->where('data', $data)
        ->where('equipe_id', $equipe_id)
        ->sum('gol_contra');
        return $quauntidadeGolsContraMarcados;
    }

    public function getCartoes()
    {
        $cartoes = Cartao::get();
        return $cartoes;
    }

    public function getCartaoAmarelo($jogador_id)
    {
        $cartaoAmarelo = JogadorCartoes::where('cartao_id', 1)
        ->where('jogador_id', $jogador_id)
        ->where('status', 0)
        ->sum('quantidade');
        return $cartaoAmarelo;
    }

        public function getCartaoVermelho($jogador_id)
    {
        $cartaoVermelho = JogadorCartoes::where('cartao_id', 2)
        ->where('jogador_id', $jogador_id)
        ->where('status', 0)
        ->count();
        return $cartaoVermelho;
    }

    public function getCartoesJogador(Request $request)
    {
        $cartoes = JogadorCartoes::select('j.nome','e.nome as equipe','t.nome as temporada', 'jogador_cartoes.*')
        ->join('jogadores As j', 'j.id', '=', 'jogador_cartoes.jogador_id')
        ->join('equipes As e', 'e.id', '=', 'jogador_cartoes.equipe_id')
        ->join('temporadas As t', 't.id', '=', 'jogador_cartoes.temporada_id')
        ->where('jogador_id', $request->jogador_id)
        ->get();
        return $cartoes;
    }

    public function pagarCartao(Request $request)
    {
        // dd($request);
        $cartao = JogadorCartoes::find($request->id);
        $cartao->status = 1;
        $cartao->update();

        $data = new class{};
        $data->jogador_cartoes = $this->getCartoesJogador($request);
        $data->jogadores = $this->getJogadores($request);
        return response()->json($data);

    }
}