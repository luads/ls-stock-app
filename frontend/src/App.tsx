import React, {useEffect, useState} from 'react';
import './App.css';
import {Container, Typography} from '@material-ui/core';
import LoginForm from './components/LoginForm';
import Dashboard from './components/Dashboard';
import UserHandler from './utils/UserHandler';
import {AppContext} from './context/AppContext';
import {AlertDetails} from './hooks/useDisplayAlert';

function App() {
  const userHandler = new UserHandler();
  const [user, setUser] = useState<string|null>(userHandler.getLoggedInUser());
  const [alertDetails, displayAlert] = useState<AlertDetails | null>(null);

  useEffect(() => {
    if (user) {
      userHandler.authenticate(user);
    }
  }, [user, userHandler]);

  const logout = () => {
    userHandler.logout();
    setUser('');
  };

  return (
    <Container component="main" maxWidth={user ? 'md' : 'sm'} className="App">
      <Typography component="h1" variant="h4">
        Stock App
      </Typography>

      { user ? (
          <AppContext.Provider value={{user, alertDetails, displayAlert}}>
            <Dashboard logout={logout} />
          </AppContext.Provider>
        ) : (
        <LoginForm authenticate={setUser} />
        )}

    </Container>
  );
}

export default App;
