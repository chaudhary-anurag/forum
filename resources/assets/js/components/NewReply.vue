<template>
  <div>
     <div v-if="signedIn">
        <div class="form-group">
           <textarea name="body" id="body" class="form-control" placeholder="Your say..." rows="5" v-model="body"></textarea>
        </div>
        <button type="submit" class="btn btn-default" @click="addReply">Post</button>
     </div>
      
     <p class="text-center" v-else>You should <a href="/login"> sign in </a> to participate in the discussion. </p>

   </div>
</template>

<script>
  import 'jquery.caret';
  import 'at.js';
  export default{
     data(){
     	return {
     		body:''
     	};
     },

     computed: {
     	signedIn(){
     		return window.App.signedIn;
     	}
     },
     
     mounted() {
         $('#body').atwho({
            at:"@",
            delay:750,
            callbacks: {
                remoteFilter: function(query,callback){
                   $.getJSON("/api/users",{name:query},function(usernames){
                        callback(usernames)
                   }); 
                }
            }
         });
     },
     
     methods: {
     	addReply(){
     		axios.post(location.pathname+'/replies',{body:this.body})
            .catch(error => { 
                /*if(error.response.data.errors==undefined){
                    flash(error.response.data,'danger');
                   }
                else{*/
                     flash(error.response.data,'danger');
                  /*  }
                }*/
            })
     		.then(({data}) => {
                this.body='';
                flash('Your reply has been posted.');
                this.$emit('created',data);
     		});
     	}
     }
  }
</script>