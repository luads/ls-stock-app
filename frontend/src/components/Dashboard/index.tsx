import React, {useState} from 'react';
import {Button, Grid, LinearProgress, Typography} from '@material-ui/core';
import StockClient from '../../clients/StockClient';
import Balance from '../Balance';
import BalanceTopUp from '../BalanceTopUp';
import SharesTable from '../SharesTable';
import {usePromiseTracker} from 'react-promise-tracker';

interface Props {
  user: string;
  logout(): void;
}

export default function Dashboard({ user, logout }: Props) {
  const [balance, setBalance] = useState<number>(0);
  const stockClient = new StockClient();
  const { promiseInProgress } = usePromiseTracker();

  return (
    <div className="dashboard">
      <Grid container spacing={3} direction="row" justify="center" alignItems="center">
        <Grid item xs={9}>
          <Typography variant="subtitle1">
            Hello {user}! <Balance balance={balance}/>
          </Typography>
        </Grid>
        <Grid item xs={3}>
          <BalanceTopUp
            user={user}
            stockClient={stockClient}
            setBalance={setBalance}
          />
          <Button onClick={logout} className="logout">Logout</Button>
        </Grid>
      </Grid>

      <SharesTable user={user} stockClient={stockClient}/>

      {promiseInProgress && <LinearProgress className="loader"/>}
    </div>
  );
}
