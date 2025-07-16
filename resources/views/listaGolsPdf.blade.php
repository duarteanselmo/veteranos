<p class="titulo">Lista de Gols</p>
<div class="row"> 
    <div class="col col-lg-12">
        <table class="tabela" width="100%">
            <tr class="cabecalho-header">
                <td width="1%">#</td>
                <td>Nome</td>
                <td style="text-align: center">Gols Marcados</td>
                <td style="text-align: center">Gols Sofridos</td>
                <td style="text-align: center">Gols Contra</td>
                <td width="2%"><div style="background-color: yellow; width: 100%; height: 15px;"></div></td>
                <td width="2%"><div style="background-color: red; width: 100%; height: 15px"></div></td>
            </tr>
                
            <?php $i = 0; ?>
            @foreach($listaGols as $key => $lista)
                <tr class="lancamentos-body {{ ($i % 2 == 0) ? 'quase-branco' : 'menos-branco' }}">
                    <td>{{$key+1}}</td>
                    <td>{{$lista->nome}}</td>
                    @if($lista->gols)
                        <td style="text-align: center">{{$lista->gols}}</td>
                    @else
                        <td style="text-align: center">-</td>
                    @endif
                    @if($lista->gols_sofridos)
                        <td style="text-align: center">{{$lista->gols_sofridos}}</td>
                    @else
                        <td style="text-align: center">-</td>
                    @endif
                    @if($lista->gol_contra)
                        <td style="text-align: center">{{$lista->gol_contra}}</td>
                    @else
                        <td style="text-align: center">-</td>
                    @endif
                    @if($lista->cartao_amarelo)
                        <td style="text-align: center">{{$lista->cartao_amarelo}}</td>
                    @else
                        <td style="text-align: center">-</td>
                    @endif
                    @if($lista->cartao_vermelho)
                        <td style="text-align: center">{{$lista->cartao_vermelho}}</td>
                    @else
                        <td style="text-align: center">-</td>
                    @endif
                </tr>
                <?php $i++; ?>
            @endforeach

        </table>
        </div>
    </div>
    <div class="page-break"></div>
    <p class="titulo">Estatísticas</p>
    <div class="row">
    <table class="tabela" width="100%">
        <tr class="cabecalho-header">
            <td>Times</td>
            <td>Vitórias</td>
            <td>Derrotas</td>
            <td>Empates</td>
            <td>Gols Pro</td>
        </tr>
        <tr class="quase-branco equipes">
            <td colspan="3"></td>
            <td style="text-align: right">Total:</td>
            <td>{{$estatisticas->golsTotal}}</td>
        </tr>
        <tr class="menos-branco">
            <td class="equipes">Time Preto</td>
            <td>{{$estatisticas->vitoriasTimePreto}}</td>
            <td>{{$estatisticas->derrotasTimePreto}}</td>
            <td>{{$estatisticas->empates}}</td>
            <td>{{$estatisticas->golsTimePreto}}</td>
        </tr>
        <tr class="quase-branco">
            <td class="equipes">Time Azul</td>
            <td>{{$estatisticas->vitoriasTimeAzul}}</td>
            <td>{{$estatisticas->derrotasTimeAzul}}</td>
            <td>{{$estatisticas->empates}}</td>
            <td>{{$estatisticas->golsTimeAzul}}</td>
        </tr>
    </table>
</div>
<div class="page-break"></div>
    <p class="titulo">Resultados da Temporada {{$temporada}}</p>
    <div class="row">
    <table class="tabela" width="100%">
            <tr class="cabecalho-header">
                <td width="15%">Placar</td>
                <td style="text-align: center;">Azul</td>
                <td style="text-align: center;">Preto</td>
                <td>Data</td>
            </tr>
                
            <?php $i = 0; ?>
            @foreach($estatisticasJogos as $key => $estatistica)
                <tr class="lancamentos-body {{ ($i % 2 == 0) ? 'quase-branco' : 'menos-branco' }}">
                    <td>Time Azul <b>{{$estatistica->golsTimeAzul}}</b> x <b>{{$estatistica->golsTimePreto}}</b> Time Preto</td>
                    <td style="text-align: center; font-weight: bold;">
                        @if($estatistica->golsTimeAzul > $estatistica->golsTimePreto)
                            <span>V</span>
                        @endif
                        @if($estatistica->golsTimeAzul < $estatistica->golsTimePreto)
                            <span>D</span>
                        @endif
                        @if($estatistica->golsTimeAzul == $estatistica->golsTimePreto)
                            <span>E</span>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        @if($estatistica->golsTimeAzul > $estatistica->golsTimePreto)
                            <span>D</span>
                        @endif
                        @if($estatistica->golsTimeAzul < $estatistica->golsTimePreto)
                            <span>V</span>
                        @endif
                        @if($estatistica->golsTimeAzul == $estatistica->golsTimePreto)
                            <span>E</span>
                        @endif
                    </td>
                    <td>{{$estatistica->data_formatada}}</td>

                </tr>
                <?php $i++; ?>
            @endforeach

        </table>

    </div>

<style scoped>
    .tabela{
        font-size: 11px;
    }
    .quase-branco {
	background: #fefefe !important;
    }
    .menos-branco {
        background: #ddd !important;
    }

    .cabecalho-header{
        font-size: 12px;
        font-weight: bold;
        background: #636363;
        color: #fff;
    }

    .lancamentos-body {
        font-size: 12px;
        background: #bbb;
    }

    .entrada{
      color: green;
    }

    .saida{
      color: red;
    }

    .saldo_positivo{
        color: green;
        font-weight: bold;
    }
    .saldo_negativo{
        color: red;
        font-weight: bold;
    }

    .total-tr{
        font-weight: bold;
        background: rgb(107, 107, 107);
        color: #fff;
        font-size: 15px;
    }

    .total-valor{
        font-weight: bold;
        background: rgb(235, 235, 235);
        color: #333;
        font-size: 15px;
    }

    .titulo{
        text-align: center;
        font-size:20px;
        font-weight: bold;
    }
    .equipes{
        font-weight: bold;
    }

    .page-break {
        page-break-after: always;
    }

</style>