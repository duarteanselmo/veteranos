<script>
    export default {
      
      data() {
          return {
          jogadores: [],
          temporada:'',
          jogador:'',
          jogador_nome:'',
          jogadorId:'',
          equipes:[],
          jogadorGols:[],
          estatisticas:[],
          temporada_estatistica_id:'',
          mensagemAlerta: '',
          mensagemAlertaSucesso:'',
          jogadorGolId:'',
          disable_store:false,
          cartoes:[],
          jogadorCartoes:[],
          jogador_cartao_id:'',

          }
      },


      methods: {
        openModalJogador(jogador = '')
        {
          const myModal = new bootstrap.Modal(document.getElementById('jogadorModal'));
          myModal.show();

          // if(jogador)
          // {
          //   document.getElementById('nome').value = jogador.nome;
          // }

          $('#jogadorModal').on('hidden.bs.modal', function(e){
            this.jogador = '';
            document.getElementById('nome').value = '';
          });

          this.jogador = jogador;
          if(this.jogador)
          {
            document.getElementById('nome').value = this.jogador.nome;
          }

        },

        openModalEstatisticas()
        {
            const myModal = new bootstrap.Modal(document.getElementById('estatisticas'));
            myModal.show();

            this.getEstatisticas();
            $('#estatisticas').on('shown.bs.modal', function (e) {
              $('#temporada_estatistica_id').val('');
            });

            $('#estatisticas').on('hidden.bs.modal', function(e){
              $('#temporada_estatistica_id').val('');
            });
        },

        getEstatisticas()
        {
          const data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            temporada_id: $('#temporada_estatistica_id').val()
          }
          jQuery.get('getEstatisticas', data, res => {
            this.estatisticas = res;
          });
        },

        getJogadores()
        {
          const data = {
              _token: $('meta[name="csrf-token"]').attr('content'),
              temporada_id: this.temporada
          }

          jQuery.get('getJogadores', data, res => {
              this.jogadores = res;
          })

        },

        getDados()
        {

          jQuery.get('getDados', res => {
              this.jogadores = res.jogadores;
              this.temporadas = res.temporadas;
              this.equipes = res.equipes;
              this.cartoes = res.cartoes;
          })
        },

        salvarJogador(){

          if(!document.getElementById('nome').value)
          {
            this.mensagemAlerta = 'O campo Nome deve ser preenchido!';
            $('#toastAlert').show();
            setTimeout(function(){
              $('#toastAlert').hide();
              this.mensagemAlerta = '';
            }, 5000); 
          }
          else
          {
            const data={
              _token: $('meta[name="csrf-token"]').attr('content'),
              nome: document.getElementById('nome').value,
              temporada_id: this.temporada
            }

            jQuery.post('salvarJogador',  data, res  => {
              this.jogadores = res;
              this.mensagemAlertaSucesso = 'Salvo com sucesso!';
                $('#toastAlertSuccess').show();
                setTimeout(function(){
                  $('#toastAlertSuccess').hide();
                  this.mensagemAlertaSucesso = '';
                }, 5000); 
            });
          }
        },

        atualizarJogador()
        {          
          if(!document.getElementById('nome').value)
          {
            this.mensagemAlerta = 'O campo Nome deve ser preenchido!';
            $('#toastAlert').show();
            setTimeout(function(){
              $('#toastAlert').hide();
              this.mensagemAlerta = '';
            }, 5000); 
          }
          else
          {
            const data = {
              _token: $('meta[name="csrf-token"]').attr('content'),
              id:this.jogador.id,
              nome: document.getElementById('nome').value,
              temporada_id: this.temporada
            }
            
            jQuery.post('atualizarJogador', data, res  => {
              this.jogadores = res;
              jQuery('#jogadorModal').modal('hide');
              this.mensagemAlertaSucesso = 'Atualizado com sucesso!';
                $('#toastAlertSuccess').show();
                setTimeout(function(){
                  $('#toastAlertSuccess').hide();
                  this.mensagemAlertaSucesso = '';
              }, 1000); 
            });
          }
        },

        openModalGols(jogadorId)
        {
          this.jogadorId = jogadorId;
          this.getGolsJogador(jogadorId);
          const myModal = new bootstrap.Modal(document.getElementById('golsModal'));
          myModal.show();
          $('#golsModal').on('shown.bs.modal', function (e) {
            $('#temporada_gols_id').val('');
          });

          $('#golsModal').on('hidden.bs.modal', function(e){
            this.jogadorId = '';
            document.getElementById('gols').value = '';
            document.getElementById('equipe').value = '';
            $('#temporada_gols_id').val('');
            document.getElementById('data').value = '';
          });
        },

        openModalCartoes(jogador)
        {
          this.jogadorId = jogador.id;
          this.getCartoesJogador(this.jogadorId);
          const myModal = new bootstrap.Modal(document.getElementById('cartoesModal'));
          myModal.show();
          $('#cartoesModal').on('shown.bs.modal', function (e) {

          });

          $('#cartoesModal').on('hidden.bs.modal', function(e){
            this.jogadorId = '';
            document.getElementById('qtd_cartao').value = '';
            document.getElementById('equipe_cartao_id').value = '';
            document.getElementById('cartao_id').value = '';
            $('#temporada_cartoes_id').val('');
            document.getElementById('data_cartao').value = '';
          });
          
        },

        getGolsJogador(jogadorId)
        {
          const data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            jogadorId: jogadorId,
            temporada_id: this.temporada
          };
          jQuery.get('getGolsJogador', data, res => {
            this.jogadorGols = res;
          });
        },
        getCartoesJogador(jogadorId)
        {
          const data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            jogador_id: jogadorId,
          };
          jQuery.get('getCartoesJogador', data, res => {
            this.jogadorCartoes = res;
          });
        },

        editGols(jogadorGols)
        {
          this.jogadorGolId = jogadorGols.id;
          document.getElementById('gols').value = jogadorGols.gols;
          document.getElementById('golsSofridos').value = jogadorGols.gols_sofridos;
          document.getElementById('equipe').value = jogadorGols.equipe_id;
          document.getElementById('data').value = jogadorGols.data;
          document.getElementById('temporada_gols_id').value = jogadorGols.temporada_id;
        },
        editCartoes(jogador_cartao)
        {
          this.jogador_cartao_id = jogador_cartao.id;
          document.getElementById('temporada_cartoes_id').value = jogador_cartao.temporada_id;
          document.getElementById('equipe_cartao_id').value = jogador_cartao.equipe_id;
          document.getElementById('cartao_id').value = jogador_cartao.cartao_id;
          document.getElementById('qtd_cartao').value = jogador_cartao.quantidade;
          document.getElementById('data_cartao').value = jogador_cartao.data;
        },

        deleteGols(){
          const data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            jogadorGolId: this.jogadorGolId,
            jogadorId: this.jogadorId,
            temporada_id: this.temporada
          }
          jQuery.post('deleteGols', data, res =>{
            this.jogadores = res.jogadores;
            this.jogadorGols = res.jogador_gols;
            this.mensagemAlertaSucesso = 'Deletado com sucesso!';
            $('#toastAlertSuccess').show();
            setTimeout(function(){
              $('#toastAlertSuccess').hide();
              this.mensagemAlertaSucesso = '';
            }, 5000);
            $('#modalConfirmDeleteGols').modal('hide');
          })
        },

        // pagarCartao(cartao_jogador_id){
        //   const data = {
        //     _token: $('meta[name="csrf-token"]').attr('content'),
        //     cartao_jogador_id: cartao_jogador_id,
        //   }
        //   jQuery.post('pagarCartao', data, res =>{
        //     this.mensagemAlertaSucesso = 'Pago com sucesso!';
        //     $('#toastAlertSuccess').show();
        //     setTimeout(function(){
        //       $('#toastAlertSuccess').hide();
        //       this.mensagemAlertaSucesso = '';
        //     }, 5000);
        //     $('#modalConfirmPagamentoCartao').modal('hide');
        //   })
        // },

        storeGol()
        {
          this.disable_store = true;
          var data = this.validator();
          if (data == false)
            return false;

          jQuery.post('storeGol', data, res => {
            this.jogadores = res.jogadores;
            this.jogadorGols = res.jogador_gols;
            document.getElementById('gols').value = '';
            document.getElementById('equipe').value = '';
            document.getElementById('data').value = '';
            document.getElementById('golsSofridos').value = '';
            document.getElementById('temporada_gols_id').value = '';
            this.disable_store = false;
            this.mensagemAlertaSucesso = 'Salvo com sucesso!';
            $('#toastAlertSuccess').show();
            setTimeout(function(){
              $('#toastAlertSuccess').hide();
              this.mensagemAlertaSucesso = '';
            }, 5000); 
          });
        },

        storeCartao()
        {
          this.disable_store = true;
          var data = this.validatorCartao();
          if (data == false)
            return false;

          jQuery.post('storeCartao', data, res => {
            this.jogadores = res.jogadores;
            this.jogadorCartoes = res.jogador_cartoes;
            this.jogador_cartao_id = '';
            document.getElementById('qtd_cartao').value = '';
            document.getElementById('equipe_cartao_id').value = '';
            document.getElementById('cartao_id').value = '';
            document.getElementById('data_cartao').value = '';
            document.getElementById('temporada_cartoes_id').value = '';
            this.disable_store = false;
            this.mensagemAlertaSucesso = 'Salvo com sucesso!';
            $('#toastAlertSuccess').show();
            setTimeout(function(){
              $('#toastAlertSuccess').hide();
              this.mensagemAlertaSucesso = '';
            }, 5000); 
          });
        },

        pagarCartao(id)
        {
          var data = 
          {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: id,
            jogador_id: this.jogadorId,
          }
          jQuery.post('pagarCartao', data, res => {
            this.jogadorCartoes = res.jogador_cartoes;
            this.jogadores = res.jogadores;
            this.mensagemAlertaSucesso = 'Atualizado com sucesso!';
          });
        },

        updateGol()
        {
          this.disable_store = true;
          var data = this.validator();
          if (data == false)
            return false;

          jQuery.post('updateGol', data, res => {
            this.jogadores = res.jogadores;
            this.jogadorGols = res.jogador_gols;
            document.getElementById('gols').value = '';
            document.getElementById('equipe').value = '';
            document.getElementById('data').value = '';
            document.getElementById('golsSofridos').value = '';
            document.getElementById('temporada_gols_id').value = '';
            this.jogadorGolId = '';
            this.disable_store = false;
            this.mensagemAlertaSucesso = 'Atualizado com sucesso!';
            $('#toastAlertSuccess').show();
            setTimeout(function(){
              $('#toastAlertSuccess').hide();
              this.mensagemAlertaSucesso = '';
            }, 5000);  
          });
        },

        validator()
        {
          if(!$('#temporada_gols_id').val())
          {
            this.mensagemAlerta = 'O campo Temporada deve ser selecionado!';
            $('#toastAlert').show();
            setTimeout(function(){
              $('#toastAlert').hide();
              this.mensagemAlerta = '';
            }, 5000);  
            this.disable_store = false;
            return false;
          }

          if(!document.getElementById('gols').value && !document.getElementById('golsSofridos').value)
          {
            this.mensagemAlerta = 'O campo Gols Marcados ou Gols Sofridos deve ser preenchido!';
            $('#toastAlert').show();
            setTimeout(function(){
              $('#toastAlert').hide();
              this.mensagemAlerta = '';
            }, 5000);  
            this.disable_store = false;
            return false;
          }

          if(!document.getElementById('equipe').value)
          {
            this.mensagemAlerta = 'O campo Time deve ser selecionado!';
            $('#toastAlert').show();
            setTimeout(function(){
              $('#toastAlert').hide();
              this.mensagemAlerta = '';
            }, 5000);  
            this.disable_store = false;
            return false;
          }

          if(!document.getElementById('data').value)
          {
            this.mensagemAlerta = 'O campo Data deve ser preenchido!';
            $('#toastAlert').show();
            setTimeout(function(){
              $('#toastAlert').hide();
              this.mensagemAlerta = '';
            }, 5000);  
            this.disable_store = false;
            return false;
          }

          const data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            jogadorId: this.jogadorId,
            gols: document.getElementById('gols').value,
            data: document.getElementById('data').value,
            id: this.jogadorGolId,
            equipe_id: document.getElementById('equipe').value,
            gols_sofridos: document.getElementById('golsSofridos').value,
            temporada_id: document.getElementById('temporada_gols_id').value
          };

          return data;
        },

        validatorCartao()
        {
          if(!$('#temporada_cartoes_id').val())
          {
            this.mensagemAlerta = 'O campo Temporada deve ser selecionado!';
            $('#toastAlert').show();
            setTimeout(function(){
              $('#toastAlert').hide();
              this.mensagemAlerta = '';
            }, 5000);  
            this.disable_store = false;
            return false;
          }

          if(!document.getElementById('equipe_cartao_id').value)
          {
            this.mensagemAlerta = 'O campo Time deve ser selecionado!';
            $('#toastAlert').show();
            setTimeout(function(){
              $('#toastAlert').hide();
              this.mensagemAlerta = '';
            }, 5000);  
            this.disable_store = false;
            return false;
          }

          if(!document.getElementById('data_cartao').value)
          {
            this.mensagemAlerta = 'O campo Data deve ser preenchido!';
            $('#toastAlert').show();
            setTimeout(function(){
              $('#toastAlert').hide();
              this.mensagemAlerta = '';
            }, 5000);  
            this.disable_store = false;
            return false;
          }

          if(!document.getElementById('cartao_id').value)
          {
            this.mensagemAlerta = 'O campo Cartão deve ser selecionado!';
            $('#toastAlert').show();
            setTimeout(function(){
              $('#toastAlert').hide();
              this.mensagemAlerta = '';
            }, 5000);  
            this.disable_store = false;
            return false;
          }

          if(!document.getElementById('qtd_cartao').value)
          {
            this.mensagemAlerta = 'O campo Quantidade deve ser preenchido!';
            $('#toastAlert').show();
            setTimeout(function(){
              $('#toastAlert').hide();
              this.mensagemAlerta = '';
            }, 5000);  
            this.disable_store = false;
            return false;
          }

          const data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            jogador_id: this.jogadorId,
            jogador_cartao_id: this.jogador_cartao_id,
            data_cartao: document.getElementById('data_cartao').value,
            id: this.jogadorCartaoId,
            equipe_id: document.getElementById('equipe_cartao_id').value,
            cartao_id: document.getElementById('cartao_id').value,
            qtd_cartao: document.getElementById('qtd_cartao').value,
            temporada_id: document.getElementById('temporada_cartoes_id').value
          };
          return data;
        },

        deleteJogador()
        {
          const data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            jogadorId: this.jogadorId,
            temporada_id: this.temporada
          }
          jQuery.post('deleteJogador', data, res =>{
            this.jogadores = res;
            this.mensagemAlertaSucesso = 'Deletado com sucesso!';
            $('#toastAlertSuccess').show();
            setTimeout(function(){
              $('#toastAlertSuccess').hide();
              this.mensagemAlertaSucesso = '';
            }, 5000);
            this.jogadorId = '';
            $('#modalConfirm').modal('hide');
          })
        },

        deleteConfirmacao(jogadorId)
        {
          this.jogadorId = jogadorId
          $('#modalConfirm').modal('show');
          
          $('#modalConfirm').on('hidden.bs.modal', function(e){
            this.jogadorId = '';
          });
        },

        modalConfirmDeleteGols(jogadorGolId)
        {
          this.jogadorGolId = jogadorGolId
          $('#modalConfirmDeleteGols').modal('show');
          
          $('#modalConfirmDeleteGols').on('hidden.bs.modal', function(e){
            this.jogadorGolId = '';
          });
        },

        modalConfirmPagamentoCartao ()
        {
          
          $('#modalConfirmPagamentoCartao').modal('show');
          
          $('#modalConfirmPagamentoCartao').on('hidden.bs.modal', function(e){
            
          });
        },

        listaGolsPdf(){
           let data = {
              temporada_id: this.temporada
            }
            window.open('listaGolsPdf?'+ $.param(data)); 
        },

        golsJogadorPdf(){
           let data = {
              temporada_id: document.getElementById('temporada_gols_id').value,
              jogadorId: this.jogadorId
            }
            window.open('golsJogadorPdf?'+ $.param(data)); 
        },
        estatisticasJogos(){
            window.open('jogos'); 
        },

      },

      watch:
      {
        temporada()
        {
          this.getJogadores();
        },

        temporada_estatistica_id()
        {
          this.getEstatisticas();
        }
      },

      mounted() {
        this.getDados();
      }
  }
</script>
<template>
  <div class="row" style="padding-top: 10px">
      <div class="col-lg-2">
          <h3>Veteranos</h3>
      </div>
      <div class="col-lg-10" style="text-align: right">
        <span class="botoes">
          <button  type = "button" class = "btn btn-primary" id="myInput" v-on:click="openModalJogador()">
          NOVO JOGADOR
          </button>
        </span>
        <span class="botoes">
          <button  type = "button" class="btn btn-primary" id="myInput" v-on:click="openModalEstatisticas()">
          ESTATISTICAS
          </button>
        </span>
        <span class="botoes">
          <button  type = "button" class="btn btn-primary" id="myInput" v-on:click="estatisticasJogos()">
          ESTATISTICAS JOGOS
          </button>
        </span>
        <span class="botoes">
          <i class="bi bi-filetype-pdf" title="Exportar PDF" v-on:click="listaGolsPdf()"></i>
        </span>
      </div>
  </div>
  <br>
  <div class="row">
    <div class="col col-lg-3">
        <label class="col-form-label" style="padding: 2%;">Temporadas</label>
        <select class="form-select" id="temporada" aria-label="Default select example" v-model="temporada">
          <option value="" selected>Selecione a temporada</option>
          <option v-for="temporada in temporadas" :value="temporada.id" :key="temporada.id">{{temporada.nome}}</option>
        </select>
    </div>
  </div>
  <br>

  <div class="row"> 
    <div class="col col-lg-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="10%">Id</th>
                    <th width="35%">Nome</th>
                    <th>Gols Marcados</th>
                    <th>Gols Sofridos</th>
                    <th>Gols Contra</th>
                    <th width="3%"><i class="bi bi-square-fill yellow"></i></th>
                    <th width="3%"><i class="bi bi-square-fill red"></i></th>
                    <th width="8%" style="text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                
                <tr v-for="(jogador, index) in jogadores" :key="jogador.id">
                    <td>{{index+1}}</td>
                    <td>{{jogador.nome}}</td>
                    <td>{{jogador.gols?jogador.gols:'-'}}</td>
                    <td>{{jogador.gols_sofridos?jogador.gols_sofridos:'-'}}</td>
                    <td>{{jogador.gol_contra?jogador.gol_contra:'-'}}</td>
                    <td>{{ jogador.cartao_amarelo?jogador.cartao_amarelo:'-' }}</td>
                    <td>{{ jogador.cartao_vermelho?jogador.cartao_vermelho:'-' }}</td>
                    <td>
                      <a style="cursor: pointer;" v-on:click="openModalJogador(jogador)"><i class="bi bi-pencil-fill" title="Editar"></i></a>
                      <a>
                        <i v-if="jogador.participacoes" class="bi bi-trash3-fill jogador_excluir_not" title="Excluir"></i>
                        <i v-else v-on:click="deleteConfirmacao(jogador.id)" class="bi bi-trash3-fill jogador_excluir"  title="Excluir"></i>
                      </a>
                      <a style="cursor: pointer;" v-on:click="openModalGols(jogador.id)"><i class="bi bi-person-fill-add" title="Adicionar Gols"></i></a>
                      <a style="cursor: pointer;" v-on:click="openModalCartoes(jogador)"><i class="bi bi-card-checklist" title="Adicionar Cartões"></i></a>
                    </td>
                </tr>
                
            </tbody>
        </table>
        </div>
    </div>

    <div class="modal fade" id="jogadorModal" tabindex="-1" aria-labelledby="jogadorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="jogadorModalLabel">Jogador</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                        <label class="col-form-label">Nome</label>
                        <input type="text" name="nome" v-model="jogador_nome" class="form-control" id="nome"/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" v-on:click="jogador?atualizarJogador():salvarJogador()">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="estatisticas" tabindex="-1" aria-labelledby="estatisticasModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content estatisticasModalLabel" style="height: 60%;">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="estatisticasModalLabel">Estatísticas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col col-lg-3">
                    <label class="col-form-label">Temporadas</label>
                    <select class="form-select" id="temporada_estatistica_id" v-model="temporada_estatistica_id">
                      <option value="" selected>Selecione a temporada</option>
                      <option v-for="temporada in temporadas" :value="temporada.id" :key="temporada.id">{{temporada.nome}}</option>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th width="35%">Times</th>
                        <th>Vitórias</th>
                        <th>Derrotas</th>
                        <th>Empates</th>
                        <th>Gols Pro</th>
                      </tr>
                      <tr>
                        <th colspan="3"></th>
                        <th style="text-align: right">Total:</th>
                        <th>{{estatisticas.golsTotal}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Time Preto</td>
                        <td>{{estatisticas.vitoriasTimePreto}}</td>
                        <td>{{estatisticas.derrotasTimePreto}}</td>
                        <td>{{estatisticas.empates}}</td>
                        <td>{{estatisticas.golsTimePreto}}</td>
                      </tr>
                      <tr>
                        <td>Time Azul</td>
                        <td>{{estatisticas.vitoriasTimeAzul}}</td>
                        <td>{{estatisticas.derrotasTimeAzul}}</td>
                        <td>{{estatisticas.empates}}</td>
                        <td>{{estatisticas.golsTimeAzul}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
              </div>
            </div>
          </div>
    </div>

  <div class="modal fade" id="cartoesModal" tabindex="-1" aria-labelledby="cartoesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" >
      <div class="modal-content cartoesModal">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="cartoesModalLabel">Adicionar Cartões</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col col-lg-3">
              <label class="col-form-label">Temporada</label>
              <select class="form-select" id="temporada_cartoes_id" >
                <option value="" selected>Selecione a temporada</option>
                <option v-for="temporada in temporadas" :value="temporada.id" :key="temporada.id">{{temporada.nome}}</option>
              </select>
            </div>

            <div class="col col-lg-3">
              <label class="col-form-label">Time</label>
              <select class="form-select" id="equipe_cartao_id">
                <option value="" selected>Selecione o time</option>
                <option v-for="equipe in equipes" :value="equipe.id" :key="equipe.id">{{equipe.nome}}</option>
              </select>
            </div>

            <div class="col col-lg-2">
              <label class="col-form-label">Cartão</label>
              <select class="form-select" id="cartao_id" >
                <option value="" selected>Selecione o cartão</option>
                <option v-for="cartao in cartoes" :value="cartao.id" :key="cartao.id">{{cartao.nome}}</option>
              </select>
            </div>
            <div class="col col-lg-2">
              <label class="col-form-label">Qtd. Cartão</label>
              <input type="number" name="qtd_cartao" class="form-control" id="qtd_cartao"/>
            </div>
            <div class="col col-lg-2">
              <label class="col-form-label">Data</label>
              <input type="date" name="data_cartao" class="form-control" id="data_cartao"/>
            </div>
          </div><br>
          <div class="row">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th width="20%">Nome</th>
                  <th>Temporada</th>
                  <th>Time</th>
                  <th width="3%"><i class="bi bi-square-fill yellow"></i></th>
                  <th width="3%"><i class="bi bi-square-fill red"></i></th>
                  <th>Status</th>
                  <th>Data</th>
                  <th>Data pagamento</th>
                  <th width="5%">Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="cartao in jogadorCartoes" :key="cartao.id">
                  <td>{{cartao.nome}}</td>
                  <td>{{cartao.temporada}}</td>
                  <td>{{cartao.equipe}}</td>
                  <td>{{cartao.cartao_id == 1 ? cartao.quantidade : '-'}}</td>
                  <td>{{cartao.cartao_id == 2 ? cartao.quantidade : '-'}}</td>
                  <td v-if="cartao.status == 0">
                    Devedor <i class="bi bi-currency-dollar" v-on:click="pagarCartao(cartao.id)" style="cursor: pointer;" title="Pagar cartão"></i>  
                  </td>
                  <td v-else>Pago</td>
                  <td>{{cartao.data}}</td>
                  <td>{{cartao.data_pagamento?cartao.data_pagamento:'-'}}</td>
                  <th>
                    <a style="cursor: pointer;" v-on:click="editCartoes(cartao)"><i class="bi bi-pencil-fill" title="Editar"></i></a>
                    <a style="cursor: pointer;" v-on:click="modalConfirmDeleteGols(gols.id)"><i class="bi bi-trash3-fill" title="Excluir"></i></a>
                  </th>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <span class="botoes">
            <i class="bi bi-filetype-pdf" title="Exportar PDF" v-on:click="golsJogadorPdf()"></i>
          </span>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <button type="button" class="btn btn-primary" :disabled="disable_store" v-on:click="jogadorGolId?updateCartao():storeCartao()">Salvar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="golsModal" tabindex="-1" aria-labelledby="golsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" >
      <div class="modal-content golsModal">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="golsModalLabel">Adicionar Quantidade de Gols</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col col-lg-3">
              <label class="col-form-label">Temporadas</label>
              <select class="form-select" id="temporada_gols_id" >
                <option value="" selected>Selecione a temporada</option>
                <option v-for="temporada in temporadas" :value="temporada.id" :key="temporada.id">{{temporada.nome}}</option>
              </select>
            </div>
            <div class="col col-lg-2">
              <label class="col-form-label">Quantidade de Gols</label>
              <input type="number" name="gols" class="form-control" id="gols"/>
            </div>
            <div class="col col-lg-2">
              <label class="col-form-label">Gols Sofridos</label>
              <input type="number" name="golsSofridos" class="form-control" id="golsSofridos"/>
            </div>
            <div class="col col-lg-3">
              <label class="col-form-label">Time</label>
              <select class="form-select" id="equipe">
                <option value="" selected>Selecione o time</option>
                <option v-for="equipe in equipes" :value="equipe.id" :key="equipe.id">{{equipe.nome}}</option>
              </select>
            </div>
            <div class="col col-lg-2">
              <label class="col-form-label">Data</label>
              <input type="date" name="data" class="form-control" id="data"/>
            </div>
          </div><br>
          <div class="row">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th width="35%">Nome</th>
                  <th>Gols</th>
                  <th>Gols Sofridos</th>
                  <th>Time</th>
                  <th>Data</th>
                  <th width="5%">Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="gols in jogadorGols" :key="gols.id">
                  <td>{{gols.nome}}</td>
                  <td>{{gols.gols}}</td>
                  <td>{{gols.gols_sofridos}}</td>
                  <td>{{gols.equipe}}</td>
                  <td>{{gols.data}}</td>
                  <th>
                    <a style="cursor: pointer;" v-on:click="editGols(gols)"><i class="bi bi-pencil-fill" title="Editar"></i></a>
                    <a style="cursor: pointer;" v-on:click="modalConfirmDeleteGols(gols.id)"><i class="bi bi-trash3-fill" title="Excluir"></i></a>
                  </th>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <span class="botoes">
            <i class="bi bi-filetype-pdf" title="Exportar PDF" v-on:click="golsJogadorPdf()"></i>
          </span>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <button type="button" class="btn btn-primary" :disabled="disable_store" v-on:click="jogadorGolId?updateGol():storeGol()">Salvar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="toast-container position-fixed bottom-0 end-0 p-3">
      <div class="toast align-items-center text-bg-primary border-0" id="toastAlert" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex" style="background-color: #e57373;">
          <div class="toast-body">
            {{mensagemAlerta}}
          </div>
        </div>
      </div>
      <div class="toast align-items-center text-bg-primary border-0" id="toastAlertSuccess" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            {{mensagemAlertaSucesso}}
          </div>
        </div>
      </div>
  </div>

  <div class="modal fade" id="modalConfirm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
              </div>
              <div class="modal-body" style="text-align: center;">
                Deseja realmente deletar esse jogador?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" v-on:click="limpar()">Não</button>
                <button type="button" class="btn btn-primary" v-on:click="deleteJogador">Sim</button>
              </div>
            </div>
          </div>
  </div>

  <div class="modal fade" id="modalConfirmDeleteGols" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
              </div>
              <div class="modal-body" style="text-align: center;">
                Deseja realmente deletar esses gols?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" v-on:click="limpar()">Não</button>
                <button type="button" class="btn btn-primary" v-on:click="deleteGols()">Sim</button>
              </div>
            </div>
          </div>
  </div>

    <div class="modal fade" id="modalConfirmPagamentoCartao" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
              </div>
              <div class="modal-body" style="text-align: center;">
                Deseja pagar esse cartão?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" v-on:click="limpar()">Não</button>
                <button type="button" class="btn btn-primary" v-on:click="pagarCartao()">Sim</button>
              </div>
            </div>
          </div>
  </div>
</template>
<style scoped>
    .botoes{
        padding: 3px;
        font-size: 12px;
    }

    .bi-pencil-fill{
      color: #0d6efd;
      padding: 1.7px;
      font-size: 15px;
    }

    .bi-trash3-fill{
      padding: 1.7px;
      color: red;
      font-size: 16px;
    }

    .bi-person-fill-add{
      padding: 1.7;
      color: green;
      font-size: 16px;
    }

    .yellow{
      color: #FFD700;
      font-size: 15px;
    }

    .red{
      color: red;
      font-size: 15px;
    }

    .bi-currency-dollar
    {
      color: green;
      font-size: 15px;
      margin-bottom: 0px;
    }

    .bi-card-checklist{
      padding: 1.7;
      padding-left: 5px;
      color: green;
      font-size: 16px;
    }

    .golsModal{
      height: 1000px;
    }
    .jogador_excluir_not{
      color: #ccc;
    }
    .jogador_excluir{
      cursor: pointer;
    }

    .bi-filetype-pdf{
      color: red;
      font-size: 25px;
      /* margin-top: 200px; */
      cursor: pointer;
    }
</style>