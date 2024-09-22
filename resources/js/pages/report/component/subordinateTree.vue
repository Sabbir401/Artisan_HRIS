<template>
    <ul>
      <li v-for="(subordinate, index) in subordinates" :key="index">
        {{ subordinate.Full_Name }}
        <SubordinateTree v-if="subordinate.children" :subordinates="subordinate.children" />
      </li>
    </ul>
  </template>
  
  <script>
  export default {
    name: "SubordinateTree",
    props: {
      subordinates: {
        type: Array,
        required: true
      }
    }
  };
  </script>