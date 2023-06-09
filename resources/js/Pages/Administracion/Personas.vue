<script setup>
import { Head } from "@inertiajs/vue3";
import Datatable from "@/Components-ISRI/Datatable.vue";
import axios from "axios";
import moment from 'moment';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import ModalAdministracionPersonasVue from '@/Components-ISRI/Administracion/ModalAdminPersona.vue';
</script>
<template>
    <Head title="Administracion" />
    <AppLayoutVue>
        <div class="sm:flex sm:justify-end sm:items-center mb-2">
            <div class="grid grid-flow-col sm:auto-cols-max sm:justify-end gap-2">
                <GeneralButton v-if="permits.insertar==1" @click="AddInformationPerson()" color="bg-green-700  hover:bg-green-800"
                    text="Agregar Elemento" icon="add" />
            </div>
        </div>
        <div class="bg-white shadow-lg rounded-sm border border-slate-200 relative">
            <header class="px-5 py-4">
                <div class="mb-4 md:flex flex-row justify-items-start">
                    <div class="mb-4 md:mr-2 md:mb-0 basis-1/4">
                        <div class="relative flex h-8 w-full flex-row-reverse div-multiselect">
                            <Multiselect v-model="tableData.length" @select="getPersonas()" :options="perPage"
                                :searchable="true" />
                            <LabelToInput icon="date" />
                        </div>
                    </div>
                    <h2 class="font-semibold text-slate-800 pt-1">Todas las personas <span class="text-slate-400 font-medium">{{
                    pagination.total
                }}</span></h2>
                </div>
             
            </header>
            <div class="overflow-x-auto">
                <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" 
                @sort="sortBy" @datos-enviados="handleData($event)">
                    <tbody class="text-sm divide-y divide-slate-200">
                        <tr v-for="persona in personas" :key="persona.id_persona">
                            <td class="px-2 first:pl-5 last:pr-5  whitespace-nowrap w-px">
                                <div class="font-medium text-slate-800">{{ persona.id_persona }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5  whitespace-nowrap w-px">
                                <div class="font-medium text-slate-800">{{ persona.dui_persona }}</div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5  whitespace-nowrap w-px">
                                <div class="font-medium text-slate-800 wrap">
                                    {{ persona.pnombre_persona }}
                                    {{ persona.snombre_persona }}
                                    {{ persona.tnombre_persona }}
                                    {{ persona.papellido_persona }}
                                    {{ persona.sapellido_persona }}
                                    {{ persona.tapellido_persona }}
                                </div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5  whitespace-nowrap w-px">
                                <div class="font-medium text-slate-800">
                                    {{ moment(persona.fecha_nac_persona).format('dddd Do MMMM YYYY') }}
                                </div>
                            </td>
                            <td class="px-2 first:pl-5 last:pr-5  whitespace-nowrap w-px">
                                <div class="font-medium text-slate-800">
                                    <div v-if="(persona.estado_persona == 1)"
                                        class="inline-flex font-medium rounded-full text-xs text-center px-2.5 py-0.5 bg-emerald-100 text-emerald-500">
                                        A
                                    </div>
                                    <div v-else
                                        class="inline-flex font-medium rounded-full text-xs text-center px-2.5 py-0.5 bg-rose-100 text-rose-600">
                                        I
                                    </div>
                                </div>
                            </td>

                            <td class="px-2 first:pl-5 last:pr-5  whitespace-nowrap w-px">
                                <div class="space-x-1">
                                    <button v-if="permits.actualizar==1" @click.stop="getInformationPersons(persona.id_persona)"
                                        class="text-slate-400 hover:text-slate-500 rounded-full">
                                        <span class="sr-only">Edit</span>
                                        <svg class="w-8 h-8 fill-current" viewBox="0 0 32 32">
                                            <path
                                                d="M19.7 8.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM12.6 22H10v-2.6l6-6 2.6 2.6-6 6zm7.4-7.4L17.4 12l1.6-1.6 2.6 2.6-1.6 1.6z">
                                            </path>
                                        </svg>
                                    </button>
                                    <!-- CAMBIAR ICONO DE BOTON POR QUE VA A SER ACTIVAR Y DESCATIVAR -->
                                    <button v-if="permits.eliminar==1" @click="enableStateForPerson(persona.id_persona, persona.estado_persona)"
                                        class="text-rose-500 hover:text-rose-600 rounded-full">
                                        <span class="sr-only">Delete</span><svg class="w-8 h-8 fill-current"
                                            viewBox="0 0 32 32">
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
                </datatable>
            </div>
        </div>
        <div class="px-6 py-8 bg-white shadow-lg rounded-sm border-slate-200 relative">
            <div>
                <nav class="flex justify-between" role="navigation" aria-label="Navigation">

                    <div class="grow text-center">
                        <ul class="inline-flex text-sm font-medium -space-x-px">
                            <li v-for="link in links" :key="link.label">
                                <span v-if="(link.label == 'Anterior')"
                                    :class="(link.active ? 'inline-flex items-center justify-center rounded-full leading-5 px-2 py-2 bg-white border border-slate-200 text-indigo-500 shadow-sm' : 'inline-flex items-center justify-center leading-5 px-2 py-2 text-slate-600 hover:text-indigo-500 border border-transparent')">

                                    <div class="flex-1 text-right ml-2">
                                        <a @click="getPersonas(link.url)"
                                            class=" btn bg-white border-slate-200 hover:border-slate-300 cursor-pointer
                                                                                                                                                                                                                                                      text-indigo-500">
                                            &lt;-<span class="hidden sm:inline">&nbsp;Anterior</span>
                                        </a>
                                    </div>
                                </span>
                                <span v-else-if="(link.label == 'Siguiente')"
                                    :class="(link.active ? 'inline-flex items-center justify-center rounded-full leading-5 px-2 py-2 bg-white border border-slate-200 text-indigo-500 shadow-sm' : 'inline-flex items-center justify-center leading-5 px-2 py-2 text-slate-600 hover:text-indigo-500 border border-transparent')">
                                    <div class="flex-1 text-right ml-2">
                                        <a @click="getPersonas(link.url)"
                                            class=" btn bg-white border-slate-200 hover:border-slate-300 cursor-pointer
                                                                                                                                                                                                                                                      text-indigo-500">
                                            <span class="hidden sm:inline">Siguiente&nbsp;</span>-&gt;
                                        </a>
                                    </div>
                                </span>
                                <span class="cursor-pointer" v-else
                                    :class="(link.active ? 'inline-flex items-center justify-center rounded-full leading-5 px-2 py-2 bg-white border border-slate-200 text-indigo-500 shadow-sm' : 'inline-flex items-center justify-center leading-5 px-2 py-2 text-slate-600 hover:text-indigo-500 border border-transparent')"><span
                                        class=" w-5" @click="getPersonas(link.url)">{{ link.label }}</span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <ModalAdministracionPersonasVue :scrollbarModalOpen="scrollbarModalOpen"
            @close-definitive="scrollbarModalOpen = false" :infoPersona="infoPerson" />

    </AppLayoutVue>
</template>
<script>
export default {
    created() {
        this.getPersonas();
        this.getPermits()
    },
    data: function (data) {
        let sortOrders = {};
        let columns = [
            { width: "10%", label: "Id", name: "id_persona", type: "text" },
            { width: "15%", label: "Dui", name: "dui_persona", type: "text" },
            { width: "35%", label: "Nombre", name: "nombre_persona", type: "text" },
            { width: "20%", label: "Fecha Nacimiento", name: "fecha_nac_persona", type: "date" },
            {
                width: "10%", label: "Estado", name: "estado_persona", type: "select",
                options: [
                {value: "1", label: "Activo"},
                {value: "0", label: "Inactivo"}
                ]
            },
            { width: "10%", label: "Acciones", name: "Acciones" },
        ];
        columns.forEach((column) => {
            if (column.name === 'id_persona')
                sortOrders[column.name] = 1;
            else
                sortOrders[column.name] = -1;
        });
        return {
            permits : [],
            scrollbarModalOpen: false,
            personas: [],
            links: [],
            lastUrl: '/personas',
            columns: columns,
            sortKey: "id_persona",
            sortOrders: sortOrders,
            perPage: ["10", "20", "30"],
            tableData: {
                draw: 0,
                length: 5,
                search: "",
                column: 0,
                dir: "desc",
            },
            pagination: {
                lastPage: "",
                currentPage: "",
                total: "",
                lastPageUrl: "",
                nextPageUrl: "",
                prevPageUrl: "",
                from: "",
                to: "",
            },
            infoPerson: [],
        };
    },
    methods: {
        getPermits(){
            var URLactual = window.location.pathname
            let data = this.$page.props.menu;
            let menu = JSON.parse(JSON.stringify(data['urls']))
            menu.forEach((value, index) => {
                value.submenu.forEach((value2, index2) => {
                if(value2.url===URLactual){
                    var array = {'insertar':value2.insertar,'actualizar':value2.actualizar,'eliminar':value2.eliminar,'ejecutar':value2.ejecutar}
                    this.permits = array
                }
                })
            })
        },
        async getPersonas(url = "/personas") {
            this.lastUrl = url;
            this.tableData.draw++;
            await axios.get(url, { params: this.tableData }).then((response) => {
                let data = response.data;
                if (this.tableData.draw == data.draw) {
                    this.links = data.data.links;
                    this.pagination.total = data.data.total
                    this.links[0].label = "Anterior";
                    this.links[this.links.length - 1].label = "Siguiente";
                    this.personas = data.data.data;
                }
            }).catch((errors) => {
                console.log(errors);
            });
        },
        sortBy(key) {
            if (key != "Acciones") {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;
                this.tableData.column = this.getIndex(this.columns, "name", key);
                this.tableData.dir = this.sortOrders[key] === 1 ? "asc" : "desc";
                this.getPersonas();
            }
        },
        getIndex(array, key, value) {
            return array.findIndex((i) => i[key] == value);
        },
        async getInformationPersons(id_persona) {
            await axios.get("/get-persona", { params: { id_persona: id_persona } })
                .then(res => {
                    this.infoPerson = res.data
                })
                .catch(err => {
                    console.error(err);
                })
            this.scrollbarModalOpen = !this.scrollbarModalOpen
        },
        async enable(id_persona, estado) {
            await axios.post("/update-state-person", { id_persona: id_persona })
                .then(res => {
                    console.log(res)
                })
                .catch(err => {
                    console.error(err);
                })
            this.getPersonas(this.lastUrl)//llamamos de nuevo el metodo para que actualize la tabla 
        },
        enableStateForPerson(id_persona, estado) {
            let state = estado == 0 ? 'habilitar' : 'deshabilitar'
            this.$swal.fire({
                title: '¿Esta seguro de ' + state + ' el registro ?',
                icon: 'question',
                iconHtml: '❓',
                confirmButtonText: 'Si, ' + state + '',
                confirmButtonColor: '#001b47',
                cancelButtonText: 'Cancelar',
                showCancelButton: true,
                showCloseButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.enable(id_persona, estado)//peticion async hace la modificacion 
                    //no la llamamos en el mismo metodo por que dejaria de ser asyn y hay problema al momento de actulizar la tabla
                    toast.info("Hecho", {
                        autoClose: 5000,
                        position: "top-right",
                        transition: "bounce",
                        toastBackgroundColor: "white",
                        icon: "✔️",
                    });
                }
            })
        },
        AddInformationPerson() {
            this.scrollbarModalOpen = !this.scrollbarModalOpen
            this.infoPerson = []
        },
        handleData(myEventData) {
            this.tableData.search = myEventData;
            this.getPersonas()
        }
    },
};
</script>
  
<style>
.wrap,
.wrap2 {
    width: 70%;
    white-space: pre-wrap;
}
</style>