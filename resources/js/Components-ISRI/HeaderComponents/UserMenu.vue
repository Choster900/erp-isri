<template>
    <div class="relative inline-flex">
        <button class="inline-flex justify-center items-center group" ref="trigger"
            @click.prevent="dropdownOpen = !dropdownOpen" :aria-expanded="dropdownOpen" aria-haspopup="true">

            <div class="w-9 h-9 rounded-full text-lg flex items-center justify-center bg-[#001c48] text-blue-300">
                <span class="uppercase text-xl text-white">{{ dynamicUsername }}</span>
            </div>

            <div class="flex items-center truncate">
                <span class="truncate ml-2 text-sm font-medium group-hover:text-slate-800">
                    {{ $page.props.auth.user.nick_usuario }}
                </span>
                <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-slate-400" viewBox="0 0 12 12">
                    <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z"></path>
                </svg>
            </div>
        </button>
        <transition enter-active-class="transition ease-out duration-200 transform"
            enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-out duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-show="dropdownOpen"
                class="origin-top-right z-10 absolute top-full min-w-44 w-52 bg-white border border-slate-200 py-1.5 rounded shadow-lg  mt-1 right-0">
                <div class="pt-0.5 pb-1 px-3 mb-1 border-slate-200">
                    <div class="font-bold text-slate-800">{{ $page.props.auth.user.name }}</div>
                    <div class="text-xs text-slate-500">{{
                        $page.props.menu ? $page.props.menu.rol : 'Bienvenido al sistema ISRI'
                    }}</div>
                </div>
                <ul ref="dropdown" @focusin="dropdownOpen = true" @focusout="dropdownOpen = false">
                    <li>
                        <DropdownLink :href="route('index.createCambiarContraseña')" method="get" as="button"
                            class="font-bold text-sm text-indigo-500 flex items-center py-0.5 px-3 hover:text-indigo-700">
                            Cambiar Contraseña
                        </DropdownLink>
                    </li>
                    <li>
                        <DropdownLink :href="route('logout')" method="post" as="button"
                            class="font-bold text-sm text-indigo-500 flex items-center py-1 px-3  hover:text-indigo-700">
                            Logout
                        </DropdownLink>
                    </li>


                </ul>
            </div>

        </transition>

    </div>
</template>

<script>
import DropdownLink from '@/Components/DropdownLink.vue';
import { ref, onMounted, onUnmounted } from 'vue'
export default {
    components: { DropdownLink },
    data() {
        return {
            dynamicUsername: "", // Aquí puedes asignar el valor dinámico que desees
        };
    },
    setup() {
        const dropdownOpen = ref(false)
        const trigger = ref(null)

        const dropdown = ref(null)

        // close on click outside
        const clickHandler = ({ target }) => {
            if (!dropdownOpen.value || dropdown.value.contains(target) || trigger.value.contains(target)) return
            dropdownOpen.value = false
        }
        const keyHandler = ({ keyCode }) => {
            if (!dropdownOpen.value || keyCode !== 27) return
            dropdownOpen.value = false
        }
        onMounted(() => {
            document.addEventListener('click', clickHandler)
            document.addEventListener('keydown', keyHandler)
        })
        onUnmounted(() => {
            document.removeEventListener('click', clickHandler)
            document.removeEventListener('keydown', keyHandler)
        })

        return {
            dropdownOpen,
            trigger,
            dropdown,
        }
    },
    created() {
        let name = (this.$page.props.auth.user.nick_usuario).split(".")
        const primerasLetras = name.map(nombre => nombre.charAt(0));
        const letrasUnidas = primerasLetras.join('');
        this.dynamicUsername = letrasUnidas
    },

}
</script>

<style></style>