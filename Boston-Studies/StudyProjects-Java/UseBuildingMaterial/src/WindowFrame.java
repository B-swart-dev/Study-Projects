/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 import java.util.Scanner;
/**
 *
 * @author lastp
 */
public class WindowFrame extends BuildingMaterial{
    private Scanner in;
    public int optionW, Wq, money;
    public String typeW;
    public double totalW;
    String statementW = "the FUCK";
    public WindowFrame() {
        in = new Scanner(System.in);
    }
    @Override
    public void totalCostPrice() {
        totalW = getCostPrice() * getQUANTITY();
    }
    public void viewWindowMenu() {
        System.out.println("-------WINDOW FRAME MENU-------");
        System.out.println("(1) Double Window @ R175 each");
        System.out.println("(2) Single Window @ R125 each");
        System.out.println("(3) Exit");
    }
       
    public void variables() {
         setQUANTITY(Wq);
         setCostPrice(money);              
    }
    public void WindowsWork() {
                switch(optionW){
                    case 1:
                        typeW = "Double Window";
                        money = 175;
                        statementW = (Wq + "x " + typeW + " @ R");
                        variables();
                        totalCostPrice();
                        break;
                    case 2:
                        typeW = "Single Window";
                        money = 125;
                        statementW = (Wq + "x " + typeW + " @ R");
                        variables();
                        totalCostPrice();
                        break;                   
                }
                   
            
    }
}