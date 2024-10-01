<template>
    <div class="carousel-3d">
        <Carousel :items-to-show="1.2" :model-value="2">
            <Slide v-for="slide in slides" :key="slide">
                <div class="carousel__item"><a :href="slide.url" v-bind:style="{ backgroundImage: 'url(' + slide.image + ')' }"></a></div>
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
                slides : [
                    {
                        image: '/images/event13/fv.jpg',
                        url: '/event13'
                    },
                    {
                        image: '/images/bnr-howto.jpg',
                        url: '/page/howto'
                    },
                    {
                        image: '/images/event14/fv.jpg',
                        url: '/event14'
                    },
                ],
            }
        }
    }
</script>
