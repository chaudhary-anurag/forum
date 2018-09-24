<template>
	<button type="submit" :class="classes" @click="toggle">
	<span class="glyphicon glyphicon-heart"></span>
    <span v-text="FavouritesCount"></span> 
    </button>
</template>

<script>
  export default {
     props: ['reply'],
     data(){
     return {
       FavouritesCount:this.reply.FavouritesCount,
       isFavourited:this.reply.isFavourited
     }
     },
     computed: {
        classes() {
           return ['btn', this.isFavourited ? 'btn-primary': 'btn-default'];
        },
        endpoint(){
          return '/replies/'+this.reply.id+'/favourites';
        }
     },
     methods: {
        toggle(){
           return this.isFavourited?this.destroy():this.create();
        },
        create(){
          axios.post(this.endpoint);
             this.isFavourited=true;
             this.FavouritesCount++;
        },
        destroy(){
           axios.delete(this.endpoint);
             this.isFavourited=false;
             this.FavouritesCount--;
        }
     }
  }
</script>
