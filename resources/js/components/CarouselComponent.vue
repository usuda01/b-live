<template>
    <div class="carousel-3d">
        <Carousel :items-to-show="1.2" :model-value="2">
            <Slide v-for="slide in slides" :key="slide">
                <div class="carousel__item"><a :href="'/event' + slide" v-bind:style="{ backgroundImage: 'url(/images/event' + slide + '/fv.jpg)' }"></a></div>
            </Slide>
            <template #addons>
                <Navigation />
            </template>
        </Carousel>
    </div>
</template>

<script>
    import 'vue3-carousel/dist/carousel.css'
    import { Carousel, Slide, Navigation } from 'vue3-carousel'

    export default {
        name: 'App',
        components: {
            Carousel,
            Slide,
            Navigation,
        },
        data () {
            return {
                slides : [9, 10, 11],
            }
        }
    }
</script>
