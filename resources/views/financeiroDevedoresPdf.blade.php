<p class="titulo">Lista de contribuintes para compra do cortador de grama</p>
<div class="row"> 
    <div class="col col-lg-12">
        <table class="tabela" width="100%">
            <tr class="cabecalho-header">
                <td>#</td>
                <td>Nome</td>
                <td>Valor</td>
                <!-- <td width="20%">Descrição</td> -->
            </tr>
                
            <?php $i = 0; ?>
            
            @foreach($lancamentos as $lancamento)
                <tr class="lancamentos-body {{ ($i % 2 == 0) ? 'quase-branco' : 'menos-branco' }}">
                    <td>{{$i+1}}</td>
                    <td>{{$lancamento->nome}}</td>
                    @if($lancamento->valor_contribuicao > 0)
                        <td class="entrada">R$ {{$lancamento->valor_contribuicao_formatado}}</td>
                    @else
                        <td >-</td>
                    @endif
                </tr>
                <?php $i++; ?>
            @endforeach

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

</style>