<template>
    <div class="game-select">
        <v-select :options="games" v-model="selected" label="name">
            <template v-slot:option="option">
                <div class="d-center">
                    <img :src='"/images/games/"+option.id+".jpg"' /> 
                    {{ option.name }}
                </div>
            </template>
            <template v-slot:selected-option="option">
                <div class="selected d-center">
                    <img :src='"/images/games/"+option.id+".jpg"' /> 
                    {{ option.name }}
                </div>
            </template>
        </v-select>
        <input type="hidden" name="game_id" :value="(selected) ? selected.id : ''">
    </div>
</template>
<script>
    import vSelect from 'vue-select';
    import 'vue-select/dist/vue-select.css';

    export default {
        components: {
            'v-select': vSelect,
        },
        props: {
            gameId: Number,
        },
        data () {
            return {
                selected: '',
                games: [],
            }
        },
        mounted () {
            this.getGames();
        },
        methods: {
            getGames() {
                const url = '/api/get-games';
                axios.get(url)
                    .then((response) => {
                        this.games = response.data;
                        this.setGame();
                    });
            },
            setGame() {
                if (this.gameId) {
                    for (let i = 0; i < this.games.length; i ++) {
                        if (this.games[i].id == this.gameId) {
                            this.selected = this.games[i];
                            break;
                        }
                    }
                }
            },
        },
    }
</script>
