<!-- <p class="titulo">Lista de Gols do jogador {{$jogador}} na temporada {{$temporada}}</p> -->
<div class="row"> 
    <div class="col col-lg-12">
      <table width="100%">
        <thead>
          <tr class="total-tr">
            <th class="alinhar_esquerda">Jogador</th>
            <th class="alinhar_esquerda">Temporada</th>
            <th class="alinhar_esquerda">Total gols pelo Time Preto</th>
            <th class="alinhar_esquerda">Total gols pelo Time Azul</th>
            <th class="alinhar_esquerda">Total</th>
          </tr>
        </thead>
        <tr class="total-valor">
          <td>{{$jogador}}</td>
          <td>{{$temporada}}</td>
          <td>{{$somaGolsPorTime->golsTimePreto}}</td>
          <td>{{$somaGolsPorTime->golsTimeAzul}}</td>
          <td>{{$somaGolsPorTime->golsTotal}}</td>
        </tr>
      </table>
      <br>
    <table class="tabela" width="100%">
              <thead>
                <tr>
                  <!-- <th class="alinhar_esquerda">Nome</th> -->
                  <th class="alinhar_esquerda">Gols</th>
                  <th class="alinhar_esquerda">Gols Sofridos</th>
                  <th class="alinhar_esquerda">Time</th>
                  <th class="alinhar_esquerda">Data</th>
                </tr>
              </thead>
              <tbody>
              <?php $i = 0; ?>
              @foreach($jogadorGols as $gols)
                <tr class="lancamentos-body {{ ($i % 2 == 0) ? 'quase-branco' : 'menos-branco' }}">
                  <!-- <td>{{$gols->nome}}</td> -->
                  <td>{{$gols->gols}}</td>
                  
                  @if($gols->gols_sofridos)
                        <td>{{$gols->gols_sofridos}}</td>
                    @else
                        <td>-</td>
                    @endif
                  <td>{{$gols->equipe}}</td>
                  <td>{{$gols->data_formatada}}</td>
                </tr>
                <?php $i++; ?>
                @endforeach
              </tbody>
            </table>
      
    </div>

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
        background:rgb(185, 183, 183);
        color: #000;
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

    .alinhar_esquerda{
      text-align: left;
    }

    .detalhes_labels > div{
        font-weight: 600;
    }
    .detalhes_labels {
        font-size: 12px;
        color: #757575;
    }

</style>