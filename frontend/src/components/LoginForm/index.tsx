import React from 'react';
import {Button, TextField} from '@material-ui/core';

export default function LoginForm() {
  return (
    <form className="login-form" noValidate>
      <TextField
        variant="outlined"
        margin="normal"
        required
        fullWidth
        id="user"
        label="Username"
        name="user"
        autoFocus
      />
      <Button
        type="submit"
        fullWidth
        variant="contained"
        color="primary"
      >
        Sign In
      </Button>
    </form>
  );
}
