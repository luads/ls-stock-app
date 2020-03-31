import React from 'react';
import {Button, Grid, Typography} from '@material-ui/core';

interface Props {
  user: string;
  logout(): void;
}

export default function Dashboard({ user, logout }: Props) {
  return (
    <div className="dashboard">
      <Grid container spacing={3} direction="row"
            justify="center"
            alignItems="center"
      >
        <Grid item xs={9}>
          <Typography variant="subtitle1">Hello {user}!</Typography>
        </Grid>
        <Grid item xs={3}>
          <Button onClick={logout}>Logout</Button>
        </Grid>
      </Grid>
    </div>
  );
}
