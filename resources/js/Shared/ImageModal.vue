<template>
    <div class="image-modal-wrapper absolute top-0 left-0 right-0 bottom-0 bg-gray-500 dark:bg-gray-900 dark:bg-opacity-80 bg-opacity-70 max-h-screen max-w-screen" v-show="showingImage" v-cloak>
        <div id="image-modal" class="min-h-full min-w-full max-w-screen max-h-screen overflow-y-auto grid place-items-center cursor-pointer">
            <i class="fa fa-times p-6 text-gray-900 dark:text-gray-200 cursor-pointer absolute top-0 right-0" @click="hideImage"></i>
            <img :src="shownImage" alt="" class="m-6 cursor-default shadow-lg" @click.away="hideImage">
        </div>
    </div>
</template>

<script>
export default {
    name: 'image-modal',
    props: {
        showingImage: false,
        shownImage: '',
    },
    methods: {
        showImage: function($event) {
            console.log($event);
            this.shownImage = $event.detail;
            this.showingImage = true;
            console.log(this);
        },
        hideImage: function($event) {
            console.log($event);
            this.showingImage = false;
            this.image = '';
        }
    },
    created() {
        window.addEventListener('open-image',this.showImage);
        window.addEventListener('close-image',this.hideImage);
    },
}
</script>

<style scoped>

</style>
