<script setup>
import Modal from "@/Components/Modal.vue";
import ModalVue from "@/Components-ISRI/AllModal/BasicModal.vue";
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import axios from 'axios';
</script>

<template>
  <ModalVue :show="showModalCreate" @close-modal="$emit('cerrar-modal')" title="Creacion de Rol "
  @close="$emit('cerrar-modal')">
  <div class="px-5 pt-4 pb-1">
    <div class="text-sm">
      <div class="mb-4">Selecciona un Sistema y escriba nombre del rol</div>
        <div class="mb-4 md:flex flex-row justify-items-start">
          <div class="mb-4 md:mr-2 md:mb-0 basis-1/2">
            <label class="block mb-2 text-xs font-light text-gray-600">
              Sistema <span class="text-red-600 font-extrabold">*</span>
            </label>
            <div class="relative font-semibold  flex h-8 w-full flex-row-reverse ">
              <Multiselect v-bind:disabled="modalDataCreate.select_sistema" class="text-xs"
                v-model="modalDataCreate.id_sistema" :options="modalDataCreate.sistemas" @select="getParentMenu()"
                placeholder="Seleccione Sistema" :searchable="true" />
              <LabelToInput icon="list" />
            </div>
          </div>

          <div class="mb-4 md:ml-0 md:mb-0 w-64">
          <TextInput v-model="modalDataCreate.nombre_rol" id="personal-information" type="text"
            placeholder="Nombre Rol">
            <LabelToInput icon="standard" forLabel="personal-information" />
          </TextInput>
        </div>
      </div>
      <div class="mb-4">Selecciona un Menú y un Submenú:</div>
      <div class="mb-4 md:flex flex-row justify-items-start">
          <div class="mb-4 md:mr-2 md:mb-0 basis-1/2">
            <label class="block mb-2 text-xs font-light text-gray-600">
              Menú <span class="text-red-600 font-extrabold">*</span>
            </label>
            <div class="relative font-semibold  flex h-8 w-full flex-row-reverse ">
              <Multiselect v-model="modalDataCreate.id_menu" :options="modalDataCreate.parentsMenu"
                @select="getChildrenMenus(modalDataCreate.parentsMenu, $event)" placeholder="Seleccione Menú"
                :searchable="true" />

              <LabelToInput icon="list" />
            </div>
          </div>

          <div class="mb-4 md:mr-2 md:mb-0 basis-1/2">
            <label class="block mb-2 text-xs font-light text-gray-600">
              Submenú <span class="text-red-600 font-extrabold">*</span>
            </label>
            <div class="relative font-semibold  flex h-8 w-full flex-row-reverse ">

              <Multiselect v-model="modalDataCreate.id_childrenMenu" :options="modalDataCreate.childrenMenus"
                @select="getChildren(modalDataCreate.childrenMenus, $event)" placeholder="Seleccione Submenú"
                :searchable="true" />

              <LabelToInput icon="list" />
            </div>
          </div>
        </div>
        <div class="mb-2">Permisos para el Submenú seleccionado:</div>
        <div class="mb-4 md:flex flex-row">
          <div class="mb-4 md:mr-2 md:mb-0">
            <label for="checbox1" class="text-sm font-bold text-gray-700">Insertar(I)
            </label>
            <checkbox v-model="modalDataCreate.insertar" v-bind:checked="modalDataCreate.insertar" class="mr-3" id="checbox1" />
            <label for="checbox2" class="text-sm font-bold text-gray-700">Actualizar(U)
            </label>
            <checkbox v-model="modalDataCreate.actualizar" v-bind:checked="modalDataCreate.actualizar" class="mr-3" id="checbox2" />
            <label for="checbox3" class="text-sm font-bold text-gray-700">Eliminar(D)
            </label>
            <checkbox v-model="modalDataCreate.eliminar" v-bind:checked="modalDataCreate.eliminar" class="mr-3" id="checbox3" />
            <label for="checbox4" class="text-sm font-bold text-gray-700">Ejecutar(E)
            </label>
            <checkbox v-model="modalDataCreate.ejecutar" v-bind:checked="modalDataCreate.ejecutar" class="mr-3" id="checbox4" />
        </div>
        </div>
        <div class="mb-4 md:flex flex-row justify-center">
          <div class="mb-4 md:mr-2 md:mb-0 px-1">
            <GeneralButton @click="addMenu()" color="bg-blue-700  hover:bg-blue-800" text="Agregar Menu" icon="add" />
          </div>
        </div>
        <div class="mb-4 md:flex flex-row justify-center">
          <div class="mb-4 md:mr-2 md:mb-0 px-1">
            <GeneralButton @click="saveRol()" color="bg-green-700  hover:bg-green-800" text="Guardar Rol" icon="add" />
          </div>
          <div class="mb-4 md:mr-2 md:mb-0 px-1">
            <GeneralButton text="Cancelar" icon="add" @click="$emit('cerrar-modal')" />
          </div>
        </div>

        <!-- Begin Table -->
        <div class="tabla-modal">
          <table class="w-full" id="tabla_modal_validacion_arranque">
            <thead class="bg-[#1F3558] text-white">
              <tr class="">
                <th class="rounded-tl-lg">#</th>
                <th>MENU</th>
                <th>SUBMENU</th>
                <th>PERMISOS</th>
                <th class="rounded-tr-lg">ACCIONES</th>
              </tr>
            </thead>
            <tbody v-for="menu in modalDataCreate.menus" :key="menu.id" class="text-sm divide-y divide-slate-200">
              <tr class="hover:bg-[#141414]/10">
                <td class="text-center">{{ menu.id }}</td>
                <td class="text-center">{{ menu.menu_padre }}</td>
                <td class="text-center">{{ menu.menu }}</td>
                <td class="text-center">
                  {{ menu.insertar == 1 ? "/I" : '' }}
                  {{ menu.actualizar == 1 ? "/U" : '' }}
                  {{ menu.eliminar == 1 ? "/D" : '' }}
                  {{ menu.ejecutar == 1 ? "/E" : '' }}
                </td>
                <td class="text-center">
                  <div class="space-x-1">
                    <button @click="deleteMenu(menu.id, menu.id_menu_padre, menu.menu_padre)"
                      class="text-rose-500 hover:text-rose-600 rounded-full">
                      <span class="sr-only">Delete</span><svg class="w-6 h-6 fill-current" viewBox="0 0 32 32">
                        <path d="M13 15h2v6h-2zM17 15h2v6h-2z">
                        </path>
                        <path
                          d="M20 9c0-.6-.4-1-1-1h-6c-.6 0-1 .4-1 1v2H8v2h1v10c0 .6.4 1 1 1h12c.6 0 1-.4 1-1V13h1v-2h-4V9zm-6 1h4v1h-4v-1zm7 3v9H11v-9h10z">
                        </path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="text-xs text-slate-500">ISRI2023</div>
      </div>
    </div>
  </ModalVue>
</template>

<script>
export default {
  props: ["showModalCreate", "modalDataCreate"],
  data: function (data) {
    return {

    }
  },
  methods: {
    //Methods for creating a new role
    cleanModalCreateInputs() {
      this.modalDataCreate.sistemas = []
      this.modalDataCreate.id_sistema = ''
      this.modalDataCreate.parentsMenu = []
      this.modalDataCreate.id_menu = ''
      this.modalDataCreate.nombre_parent_menu = ''
      this.modalDataCreate.childrenMenus = []
      this.modalDataCreate.nombre_rol = ''
      this.modalDataCreate.menus = []
      this.modalDataCreate.select_sistema = false
      this.modalDataCreate.insertar=false
      this.modalDataCreate.actualizar=false
      this.modalDataCreate.eliminar=false
      this.modalDataCreate.ejecutar=false
    },
    addMenu(){
      let formValid=true
      let msg
      if(this.modalDataCreate.id_menu==''){
        msg='Debe seleccionar Menú.'
        formValid=false
      }else{
        if(this.modalDataCreate.id_childrenMenu==''){
          msg='Debe seleccionar Submenú.'
          formValid=false
        }else{
          if(this.modalDataCreate.insertar==false && this.modalDataCreate.actualizar==false && this.modalDataCreate.eliminar==false &&this.modalDataCreate.ejecutar==false){
            msg='Debe seleccionar al menos un permiso.'
            formValid=false
          }
        }
      }
      if(!formValid){
        this.$swal.fire({
          title: 'Información incompleta',
          text: msg,
          icon: 'warning',
          timer: 5000
        })
      }else{
        //Insertando nuevo array en tabla de menus
        var array = {
          id: this.modalDataCreate.id_childrenMenu,
          id_menu_padre: this.modalDataCreate.id_menu,
          menu_padre: this.modalDataCreate.nombre_parent_menu, 
          menu: this.modalDataCreate.name_childrenMenu,
          insertar:this.modalDataCreate.insertar,
          actualizar:this.modalDataCreate.actualizar,
          eliminar:this.modalDataCreate.eliminar,
          ejecutar:this.modalDataCreate.ejecutar
        }
        this.modalDataCreate.menus.push(array)
        this.modalDataCreate.id_childrenMenu = ''
        this.modalDataCreate.name_childrenMenu= ''
        this.modalDataCreate.insertar=false
        this.modalDataCreate.actualizar=false
        this.modalDataCreate.eliminar=false
        this.modalDataCreate.ejecutar=false
        this.modalDataCreate.childrenMenus.forEach((value, index) => {
          if (value.value == array.id) {
            this.modalDataCreate.childrenMenus.splice(index, 1)
          }
        })
        //Verificando si el select de menus hijos esta vacio para borrar el respectivo menu padre
        if (this.modalDataCreate.childrenMenus == '') {
          this.modalDataCreate.parentsMenu.forEach((value, index) => {
            if (value.value == this.modalDataCreate.id_menu) {
              this.modalDataCreate.parentsMenu.splice(index, 1)
              this.modalDataCreate.id_menu = ""
              this.modalDataCreate.id_childrenMenu = ""
            }
          })
          this.modalDataCreate.id_menu = ''
      }
      }
    },
    getParentMenu() {
      this.modalDataCreate.select_sistema = true
      axios.post('/parents-menu-all', { id_sistema: this.modalDataCreate.id_sistema })
        .then((response) => {
          this.modalDataCreate.parentsMenu = response.data.parentsMenu
        })
        .catch((errors) => {
            let msg = this.manageError(errors)
            this.$swal.fire({
              title: 'Operación cancelada',
              text: msg,
              icon: 'warning',
              timer:5000
            })
        })
    },
    getChildrenMenus: function (data, event) {

      let finalLabel
      data.forEach((value, index) => {
        if (value.value == event) {
          finalLabel = value.label
        }
      })
      this.modalDataCreate.nombre_parent_menu = finalLabel
      axios.post('/children-menus', { id_menu: this.modalDataCreate.id_menu, id_menus_rol: this.modalDataCreate.id_menus_rol })
        .then((response) => {
          this.modalDataCreate.childrenMenus = response.data.childrenMenus
          if (this.modalDataCreate.menus != "") {
            this.modalDataCreate.menus.forEach((value1, index1) => {
              this.modalDataCreate.childrenMenus.forEach((value2, index2) => {
                if (value2.value == value1.id) {
                  this.modalDataCreate.childrenMenus.splice(index2, 1)
                }
              })
            });
          }
        })
        .catch((errors) => {
            let msg = this.manageError(errors)
            this.$swal.fire({
              title: 'Operación cancelada',
              text: msg,
              icon: 'warning',
              timer:5000
            })
        })
    },
    getChildren(data, event) {
      let finalLabel
      data.forEach((value, index) => {
        if (value.value == event) {
          finalLabel = value.label
        }
      })
      this.modalDataCreate.name_childrenMenu=finalLabel
      
      

    },
    deleteMenu(id_menu, id_padre, nombre_padre) {
      console.log(id_menu, id_padre, nombre_padre);
      let arraySelectParents = { value: id_padre, label: nombre_padre }
      this.modalDataCreate.menus.forEach((value, index) => {
        if (value.id == id_menu) {
          this.modalDataCreate.menus.splice(index, 1)
        }
      })
      if (this.modalDataCreate.parentsMenu == '') {
        this.modalDataCreate.parentsMenu.push(arraySelectParents)
      } else {
        let isInArray = false
        this.modalDataCreate.parentsMenu.forEach((value2, index2) => {
          if (value2.value == id_padre) {
            isInArray = true
          }
        })
        if (!isInArray) {
          this.modalDataCreate.parentsMenu.push(arraySelectParents)
        }
      }
      this.modalDataCreate.childrenMenus = ''
      this.modalDataCreate.id_childrenMenu = ''
      this.modalDataCreate.id_menu = ''

    },
    saveRol() {
      if (this.modalDataCreate.nombre_rol != "" && this.modalDataCreate.menus != "") {
        this.$swal.fire({
          title: 'Esta seguro de guardar el rol',
          icon: 'question',
          iconHtml: '✅',
          confirmButtonText: 'Si, Guardar',
          confirmButtonColor: '#15803D',
          cancelButtonText: 'Cancelar',
          showCancelButton: true,
          showCloseButton: true
        }).then((result) => {
          if (result.isConfirmed) {
            axios.post("/create-rol", {
              id_sistema: this.modalDataCreate.id_sistema,
              nombre_rol: this.modalDataCreate.nombre_rol,
              menus: this.modalDataCreate.menus,
            }).then((response) => {
              this.$swal.fire({
                text: response.data.mensaje,
                icon: 'success',
                timer: 3000
              })
              //this.cleanModalCreateInputs()
              this.$emit("update-table")
              this.$emit('cerrar-modal')
            }).catch((errors) => {
            let msg = this.manageError(errors)
            this.$swal.fire({
              title: 'Operación cancelada',
              text: msg,
              icon: 'warning',
              timer:5000
            })
        })
          }
        })
      } else {
        let msg = "Debe "
        if (this.modalDataCreate.nombre_rol == "") {
          msg = msg + "escribir el nombre para el nuevo rol "
          if (this.modalDataCreate.menus == "") {
            msg = msg + "y agregar al menos un menu."
          }
        } else {
          msg = msg + "agregar al menos un menu."
        }
        this.$swal.fire({
          title: 'Información incompleta',
          text: msg,
          icon: 'warning',
          timer: 5000
        })
      }
    },
  },
};
</script>