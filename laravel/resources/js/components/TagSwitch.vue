<template>
    <div id="app">
        <div id="nav">
            <router-link to="/kanban">Kanban</router-link> |
            <router-link to="/gantt">Gantt</router-link>
            <p class="text-red-700 mt-1">※ タグをクリックすると表示が切り替え可能です</p>
        </div>
        <router-view/>
    </div>
</template>

<style>
#app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
}

#nav {
    padding: 30px;
}

#nav a {
    font-weight: bold;
    color: #2c3e50;
}

#nav a.router-link-exact-active {
    color: #42b983;
}
</style>