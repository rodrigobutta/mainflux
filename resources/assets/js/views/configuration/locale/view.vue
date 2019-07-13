<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">Configuration</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">Home</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration/basic">Configuration</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration/locale">Locale</router-link></li>
                    <li class="breadcrumb-item active">Translation</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2 col-lg-2 col-md-2">
                                <ul class="list-group">
                                    <li v-for="mdl in modules" :class="[mdl === module ? 'active' : '', 'list-group-item']"><router-link :to="getModuleLink(mdl)" exact>{{getName(mdl)}}</router-link></li>
                                </ul>
                            </div>
                            <div class="col-10 col-lg-10 col-md-10">
                                <show-tip module="locale" tip="tip_translation"></show-tip>
                                <div v-if="getWordCount">
                                    <div v-for="(word,index) in words">
                                        <div v-if="typeof word === 'object'">
                                            <div class="form-group" v-for="(wrd,i) in word">
                                                <label for="">{{trans(module+'.'+index+'.'+i)}}</label>
                                                <!-- <label for="">{{index}}_{{i}}</label> -->
                                                <input class="form-control" type="text" value="" v-model="words[index][i]" :name="`${index}_${i}`">
                                            </div>
                                        </div>
                                        <div class="form-group" v-else>
                                            <label for="">{{trans(module+'.'+index)}}</label>
                                            <!-- <label for="">{{index}}</label> -->
                                            <input class="form-control" type="text" value="" v-model="words[index]" :name="index">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-info btn-sm pull-right" @click="saveTranslation">Save</button>
                                    </div>
                                </div>
                                <div v-else>
                                    <p class="alert alert-danger">No record found!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        data() {
            return {
                modules: {},
                words: {},
                module: (this.$route.params.module) ? this.$route.params.module : 'auth'
            };
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('multilingual')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            this.fetchWords();
        },
        methods: {
            fetchWords(){
                axios.post('/api/locale/fetch',{
                    locale: this.$route.params.locale,
                    module: this.module
                    })
                    .then(response => response.data)
                    .then(response => {
                        this.modules = response.modules,
                        this.words = response.words
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getName(name){
                name = helper.ucword(name);
                return name.replace(/_/g, ' ');
            },
            getModuleLink(module){
                return '/configuration/locale/'+this.$route.params.locale+'/'+module
            },
            saveTranslation(){
                axios.post('/api/locale/translate',{
                    locale: this.$route.params.locale,
                    module: this.module,
                    words: this.words
                }).then(response => response.data)
                .then(response => {
                    toastr.success(response.message);
                }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
            }
        },
        watch: {
            '$route.params.module'(newModule, oldModule) {
                this.module = newModule;
                this.fetchWords();
            }
        },
        computed: {
            getWordCount(){
                return _size(this.words);
            }
        }
    }
</script>
<style>
    .list-group-item .active {color:#ffffff;}
</style>
