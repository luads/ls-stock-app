import React, {FormEvent, useState} from 'react';
import {Button, TextField} from '@material-ui/core';

interface Props {
  authenticate(user: string): void;
}

export default function LoginForm({ authenticate }: Props) {
  const [username, setUsername] = useState<string>();

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();

    if (username) {
      authenticate(username);
    }
  };

  return (
    <form className="login-form" method="POST" noValidate onSubmit={handleSubmit}>
      <TextField
        onChange={event => setUsername(event.target.value)}
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
