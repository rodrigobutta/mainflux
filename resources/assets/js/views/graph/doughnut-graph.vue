<script>
    import { Doughnut } from 'vue-chartjs'

    export default {
        extends: Doughnut,
        props: ['graph'],
        mounted(){
        },
        watch:{
          graph(val){
            this.renderChart(this.graph)
          }
        }
    }
</script>
