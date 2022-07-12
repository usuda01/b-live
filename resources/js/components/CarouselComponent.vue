<template>
    <div class="carousel-3d">
        <swiper class="swiper" :options="swiperOption">
            <swiper-slide><a href="/event5" style="background-image: url('/images/event5/fv.jpg');"></a></swiper-slide>
            <swiper-slide><a href="/event6" style="background-image: url('/images/event6/fv.jpg');"></a></swiper-slide>
            <div class="swiper-pagination" slot="pagination"></div>
            <div class="swiper-button-prev" slot="button-prev"></div>
            <div class="swiper-button-next" slot="button-next"></div>
        </swiper>
    </div>
</template>

<script>
    import { Swiper, SwiperSlide, directive } from 'vue-awesome-swiper'
    import 'swiper/css/swiper.css'

    export default {
        components: {
            Swiper,
            SwiperSlide
        },
        directives: {
            swiper: directive
        },
        data () {
            return {
                swiperOption: {
                    centeredSlides: true,
                    effect: 'coverflow',
                    grabCursor: true,
                    initialSlide: 1,
                    slidesPerView: 'auto',
                    coverflowEffect: {
                        rotate: 50,
                        stretch: 0,
                        depth: 100,
                        modifier: 1,
                        slideShadows : true
                    },
                    pagination: {
                        el: '.swiper-pagination'
                    },
                    navigation: {
                      nextEl: '.swiper-button-next',
                      prevEl: '.swiper-button-prev'
                    }
                }
            }
        }
    }
</script>
