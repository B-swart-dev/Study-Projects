/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author lastp
 */
public abstract class BuildingMaterial {
	private String productDescription;
	private int quantity;
	private double costPrice;
	
public String getProductDescription() {
	return productDescription;
}//end getProductDescription

public int getQUANTITY(){
	return quantity;
}//end getQUANTITY

public void setQUANTITY(int quantity) {
	this.quantity = quantity;
}//end setQUANTITY

public double getCostPrice() {
	return costPrice;
}//end getCostPrice			

public void setCostPrice(double costPrice) {
	this.costPrice = costPrice;
}//end setCostPrice		

public abstract void totalCostPrice();

}//end abstract class BuildingMaterial