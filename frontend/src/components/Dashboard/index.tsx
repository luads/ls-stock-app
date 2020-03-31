import React, {useState} from 'react';
import {Button, Grid, LinearProgress, Typography} from '@material-ui/core';
import StockClient from '../../clients/StockClient';
import Balance from '../Balance';

interface Props {
  user: string;
  logout(): void;
}

export default function Dashboard({ user, logout }: Props) {
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const stockClient = new StockClient();

  return (
    <div className="dashboard">
      <Grid container spacing={3} direction="row" justify="center" alignItems="center">
        <Grid item xs={9}>
          <Typography variant="subtitle1">
            Hello {user}! <Balance user={user} stockClient={stockClient} setIsLoading={setIsLoading}/>
          </Typography>
        </Grid>
        <Grid item xs={3}>
          <Button onClick={logout} className="logout">Logout</Button>
        </Grid>
      </Grid>

      { isLoading && <LinearProgress className="loader" /> }
    </div>
  );
}
