import React, {FormEvent, useEffect} from 'react';
import {Button, Dialog, DialogActions, DialogContent, DialogTitle, TextField} from '@material-ui/core';
import AttachMoneyIcon from '@material-ui/icons/AttachMoney';
import {trackPromise} from 'react-promise-tracker';
import StockClient from '../../clients/StockClient';

interface Props {
  user: string;
  stockClient: StockClient;
  setBalance(balance: number): void;
}

export default function BalanceTopUp({ user, stockClient, setBalance }: Props) {
  const [isModelOpen, setModalOpen] = React.useState(false);
  const [topUpBalance, setTopUpBalance] = React.useState<number>(0);

  const handleModalClose = () => {
    setModalOpen(false);
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();

    if (!topUpBalance) {
      handleModalClose();
      return;
    }

    const newBalance = await trackPromise(stockClient.sendTransaction(user, topUpBalance));
    setBalance(newBalance);

    handleModalClose();
  };

  useEffect(() => {
    const fetchBalance = async () => {
      setBalance(await trackPromise(stockClient.getBalance(user)));
    };

    fetchBalance();
  }, []);

  return (
    <>
      <Button className="top-up" color="primary" onClick={() => setModalOpen(true)} startIcon={<AttachMoneyIcon/>}>
        Top up
      </Button>

      <Dialog open={isModelOpen} onClose={handleModalClose} aria-labelledby="form-dialog-title">
        <form onSubmit={handleSubmit}>
          <DialogTitle id="form-dialog-title">Top up your balance</DialogTitle>
          <DialogContent>
            <TextField
              autoFocus
              margin="dense"
              id="balance-transaction"
              label="Amount"
              type="number"
              fullWidth
              inputProps={{ 'step': 0.01 }}
              onChange={(e) => setTopUpBalance(parseFloat(e.target.value))}
            />
          </DialogContent>
          <DialogActions>
            <Button onClick={handleModalClose}>
              Cancel
            </Button>
            <Button type="submit" color="primary">
              Send
            </Button>
          </DialogActions>
        </form>
      </Dialog>
    </>
  );
}
