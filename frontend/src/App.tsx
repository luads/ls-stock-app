import React from 'react';
import './App.css';
import {Container, Typography} from '@material-ui/core';
import LoginForm from './components/LoginForm';


function App() {
  return (
    <Container component="main" maxWidth="sm" className="App">
      <Typography component="h1" variant="h5">
        Stock App
      </Typography>
      <LoginForm />
    </Container>
  );
}

export default App;
