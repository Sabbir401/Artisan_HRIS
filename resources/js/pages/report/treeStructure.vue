<template>
    <div>
        <SubordinateTree :subordinates="subordinates" />
    </div>
</template>

<script>
import SubordinateTree from "./component/subordinateTree.vue";
import api from '@/api';

export default {
  components: { SubordinateTree, api },
  data() {
    return {
      subordinates: []
    };
  },
  mounted() {
    this.getSubordinates();
  },
  methods: {
    async getSubordinates () {
      const subordinates = await api.get("/getTree");
      this.subordinates = subordinates;
    }
  }
};
</script>
