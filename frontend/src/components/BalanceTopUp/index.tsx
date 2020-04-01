import React, {FormEvent, useContext, useState} from 'react';
import {Button, Dialog, DialogActions, DialogContent, DialogTitle, TextField} from '@material-ui/core';
import AttachMoneyIcon from '@material-ui/icons/AttachMoney';
import {trackPromise} from 'react-promise-tracker';
import StockClient from '../../clients/StockClient';
import {AlertDetails, useDisplayAlert} from '../../hooks/useDisplayAlert';
import {AppContext} from '../../context/AppContext';

interface Props {
  stockClient: StockClient;
  setBalance(balance: number): void;
}

export default function BalanceTopUp({ stockClient, setBalance }: Props) {
  const { user } = useContext(AppContext);
  const displayAlert = useDisplayAlert();
  const [isModalOpen, setModalOpen] = useState(false);
  const [topUpBalance, setTopUpBalance] = useState<number>(0);

  const handleModalClose = () => {
    setModalOpen(false);
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();

    if (!topUpBalance) {
      handleModalClose();
      return;
    }

    let newBalance;

    try {
      newBalance = await trackPromise(stockClient.sendTransaction(user, topUpBalance));
    } catch (error) {
      displayAlert({ severity: 'error', text: error.message });
      handleModalClose();
      return;
    }

    displayAlert({ severity: 'success', text: 'Balance transaction handled successfully!'} as AlertDetails);
    setBalance(newBalance);

    handleModalClose();
  };

  return (
    <>
      <Button className="top-up" color="primary" onClick={() => setModalOpen(true)} startIcon={<AttachMoneyIcon/>}>
        Top up
      </Button>

      <Dialog open={isModalOpen} onClose={handleModalClose} aria-labelledby="form-dialog-title">
        <form onSubmit={handleSubmit}>
          <DialogTitle id="form-dialog-title">Top up your balance</DialogTitle>
          <DialogContent>
            <TextField
              required={true}
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
