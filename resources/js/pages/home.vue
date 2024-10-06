<template>
    <div class="home-page">
      <!-- Header Section -->
      <header class="header">
        <div class="header-content">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/React-icon.svg/512px-React-icon.svg.png" class="logo" alt="HRM Logo" />
          <nav class="nav">
            <button class="button signup-button" @click="navigateTo('signup')">Sign Up</button>
            <button class="button login-button" @click="navigateTo('login')">Login</button>
          </nav>
        </div>
      </header>
  
      <!-- Hero Section -->
      <main class="main-content">
        <div class="main-text">
          <h1 class="main-title">Welcome to HRIS Solutions</h1>
          <p class="main-subtitle">
            Revolutionizing the way you manage your workforce. Stay organized,
            connected, and efficient.
          </p>
        </div>
        <!-- GIF Container -->
        <div class="gif-container">
          <img
            src="/public/storage/uploads/home_design.png"
            alt="HRM Animation"
            class="gif"
          />
        </div>
      </main>

      <!-- Animated Background Elements -->
      <div class="animated-elements">
        <div class="circle"></div>
        <div class="square"></div>
        <div class="triangle"></div>
      </div>
  
      <!-- Features Section -->
      <section class="features-section">
        <div class="feature-card">
          <img src="https://randomuser.me/api/portraits/men/32.jpg" class="avatar" alt="Feature Avatar" />
          <h2 class="feature-title">Employee Management</h2>
          <p class="feature-description">
            Effortlessly manage your team, assign roles, track performance, and
            stay on top of employee details.
          </p>
        </div>
  
        <div class="feature-card">
          <img src="https://randomuser.me/api/portraits/women/44.jpg" class="avatar" alt="Feature Avatar" />
          <h2 class="feature-title">Attendance Tracking</h2>
          <p class="feature-description">
            Automated attendance management with real-time tracking and detailed
            reports.
          </p>
        </div>
  
        <div class="feature-card">
          <img src="https://randomuser.me/api/portraits/men/64.jpg" class="avatar" alt="Feature Avatar" />
          <h2 class="feature-title">Leave Management</h2>
          <p class="feature-description">
            Handle leave requests, approvals, and maintain a transparent leave
            policy.
          </p>
        </div>
      </section>
  
    </div>
  </template>
  
  <script>
  export default {
    name: "HomePage",
    methods: {
      navigateTo(page) {
        this.$router.push({ name: page });
      },
    },
  };
  </script>
  
  <style scoped>
  /* Reset and General Styles */
 *{
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
 }
  
  .home-page {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
    background: linear-gradient(135deg, #4c4cfc, #23d5ab);
    overflow: hidden;
  }
  
  /* Header Styles */
  .header {
    width: 100%;
    padding: 1rem 2rem;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
  }
  
  .header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
  }
  
  .logo {
    width: 60px;
  }
  
  .nav {
    display: flex;
    gap: 1rem;
  }
  
  .button {
    padding: 0.8rem 2rem;
    font-size: 1rem;
    cursor: pointer;
    border: none;
    border-radius: 25px;
    transition: 0.3s ease-in-out;
  }
  
  .signup-button {
    background: #ff7675;
    color: #fff;
  }
  
  .signup-button:hover {
    background: #e65050;
  }
  
  .login-button {
    background: #55efc4;
    color: #fff;
  }
  
  .login-button:hover {
    background: #35d4a2;
  }
  
  /* Main Content Styles */
  .main-content {
    padding: 2rem;
    text-align: center;
  }
  
  .main-title {
    font-size: 3rem;
    font-weight: 700;
    color: #ffffff;
  }
  
  .main-subtitle {
    font-size: 1.2rem;
    color: #f5f5f5;
    max-width: 700px;
    margin: 1rem auto 2rem;
  }
  
  .gif-container {
    margin: 2rem 0;
    margin-bottom: 7rem;
  }
  
  .gif {
    width: 300px;
    height: auto;
    border-radius: 15px;
    box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.2);
  }
  
  /* Feature Section */
  .features-section {
    display: flex;
    justify-content: center;
    gap: 2rem;
    padding: 3rem;
  }
  
  .feature-card {
    background: #ffffff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: 0.3s;
  }
  
  .feature-card:hover {
    transform: translateY(-5px);
  }
  
  .avatar {
    width: 80px;
    border-radius: 50%;
    margin-bottom: 1rem;
  }
  
  .feature-title {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
  }
  
  .feature-description {
    font-size: 1rem;
    color: #666;
  }
  
  /* CTA Section */
  .cta-section {
    display: flex;
    justify-content: space-around;
    align-items: center;
    background: #333;
    color: #fff;
    padding: 3rem;
  }
  
  .cta-text h2 {
    font-size: 2rem;
  }
  
  .cta-button {
    margin-top: 1rem;
    padding: 0.8rem 2rem;
    background: #ff7675;
    color: #fff;
    border: none;
    border-radius: 25px;
  }
  
  .cta-avatar img {
    width: 100px;
  }
  
  /* Animated Background Elements */
  .animated-elements {
    transform: translate(-50%, -50%);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .circle,
  .square,
  .triangle {
    width: 70px;
    height: 70px;
    margin: 1rem;
    animation: float 5s infinite ease-in-out;
  }
  
  .circle {
    background-color: #ffffff;
    border-radius: 50%;
  }
  
  .square {
    background-color: #ffffff;
    border-radius: 15px;
  }
  
  .triangle {
    width: 0;
    height: 0;
    border-left: 35px solid transparent;
    border-right: 35px solid transparent;
    border-bottom: 70px solid #ffffff;
  }
  
  /* Animation Keyframes */
  @keyframes float {
    0%,
    100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-20px);
    }
  }
  </style>
  